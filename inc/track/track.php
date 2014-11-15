<?php
namespace track;

use classes\collection;
use classes\coordinate_bound;
use classes\db;
use classes\get;
use classes\kml;
use classes\session;
use object\club;
use object\flight;
use object\glider;
use object\log;
use object\pilot;

class track
{
    public $error;
    
    /** @var  log $log */
    protected $log;
    public $id;
    public $temp = false;
    
    /** @var \coordinate_set $coordinate_set */
    public $coordinate_set;
    
    /** @var glider club */
    private $glider;
    
    /** @var flight club */
    private $flight;
    
    /** @var pilot club */
    private $pilot;
    
    /** @var distance_map club */
    private $distance_map;
    
    /** @var club club */
    private $club;
    
    /** @var \task */
    public $od, $or, $tr, $ft;
    
    public function __construct($id = null) {
        if ($id === null) {
            $this->temp = true;
            $this->id = time();
        } else {
            $this->id = $id;
        }
        $this->pilot = new pilot();
        $this->club = new club();
        $this->glider = new glider();
        $this->bounds = new coordinate_bound();
        if (!is_dir($this->get_file_loc())) {
            mkdir($this->get_file_loc());
        }
        $this->log = new log(log::DEBUG, $this->get_file_loc() . '/info-' . time() . '.txt');
    }
    
    public function set_flight(flight $flight) {
        $this->flight = $flight;
    }
    
    public static function move_temp_files($temp_id, $new_id) {
        $track = new track($new_id);
        $old_dir = $track->get_file_loc($temp_id, true);
        $new_dir = $track->get_file_loc($new_id, false);
        if (!file_exists($new_dir)) {
            mkdir($new_dir);
        }
        copy($old_dir . '/track.igc', $new_dir . '/track.igc');
        copy($old_dir . '/track_backup.igc', $new_dir . '/track_backup.igc');
    }
    
    public function calculate() {
        db::query('SET wait_timeout=1200');
        set_time_limit(0);
        $this->distance_map = new \distance_map($this->coordinate_set);
        $this->od = $this->distance_map->score_open_distance_3tp();
        $this->log->info("Open Distance Calculated, Dist:{$this->od->get_distance(true) } Cords={$this->od->get_gridref() }", $this);
        $this->or = $this->distance_map->score_out_and_return();
        $this->log->info("Out and Return Calculated, Dist:{$this->or->get_distance(true) } Cords={$this->or->get_gridref() }");
        
        //        $this->track_flat_triangles();
        //        $this->log->info("Flat Triangle Calculated, Dist:{$this->ft->get_distance()} Cords={$this->ft->get_gridref()}", $this);
        $this->tr = $this->distance_map->score_triangle();
        $this->log->info("Triangle Calculated, Dist:{$this->tr->get_distance(true) } Cords={$this->tr->get_gridref() }", $this);
        $this->set_info();
    }
    
    public function check_date() {
        $current_time = time();
        $closure_time = $current_time - (31 * 24 * 60 * 60);
        if ($this->get_timestamp() >= $closure_time && $this->get_timestamp() <= $current_time) {
            $this->log->info("Date is within 1 month");
            return true;
        } else {
            $this->log->error("Date is outside of 1 month");
            return false;
        }
    }
    
    public function cleanup() {
        unset($this->flight);
        unset($this->coordinate_set);
        unset($this->or);
        unset($this->od);
        unset($this->tr);
    }
    
    public function create_from_upload() {
        if (isset($_FILES['kml']['tmp_name'])) {
            $dir = $this->get_file_loc();
            if (!file_exists($dir)) {
                mkdir($dir);
            } else {
                $files = glob($dir . '*', GLOB_MARK);
                foreach ($files as $file) {
                    if (!is_dir($file)) {
                        unlink($file);
                    }
                }
            }
            move_uploaded_file($_FILES['kml']['tmp_name'], $dir . '/track.igc');
            copy($dir . '/track.igc', $dir . '/track_backup.igc');
        }
    }
    
    public function generate() {
        if ($this->parse_IGC()) {
            $this->calculate();
            $this->generate_output_files();
            
            $this->flight->duration = $this->get_time();
            
            $this->flight->od_score = $this->od->get_distance();
            $this->flight->od_time = $this->od->get_duration();
            $this->flight->od_coordinates = $this->od->get_gridref();
            
            $this->flight->or_score = $this->or->get_distance();
            $this->flight->or_time = $this->or->get_duration();
            $this->flight->or_coordinates = $this->or->get_gridref();
            
            $this->flight->tr_score = $this->tr->get_distance();
            $this->flight->tr_time = $this->tr->get_duration();
            $this->flight->tr_coordinates = $this->tr->get_gridref();
            
            /*
            $this->parent_flight->ft_score = $this->ft->get_distance();
            $this->parent_flight->ft_time = $this->ft->get_time();
            $this->parent_flight->ft_coordinates = $this->ft->get_coordinates();*/
            return true;
        } else {
            return false;
        }
    }
    
    public function generate_js() {        
        if ($this->od) {
            $formatter = new \formatter_js($this->coordinate_set, $this->id, $this->od, $this->or, $this->tr);
        } else {
            $formatter = new \formatter_js($this->coordinate_set, $this->id);
        }
        fwrite(fopen($this->get_file_loc() . '/track.js', 'w'), $formatter->output());
    }
    
    public function generate_kml_raw() {
        $kml = new \formatter_kml($this->coordinate_set, "", $this->od, $this->or, $this->tr);
        $path = $this->get_file_loc() . '/track.kml';
        $path = root . get::trim_root($path);
        file_put_contents($path, $kml->output());
        return $path;
    }
    
    public function generate_kml_split() {
        $formatter = new \formatter_kml_split($this->coordinate_set);
        $path = $this->get_file_loc() . '/track.kml';
        $path = root . get::trim_root($path);
        file_put_contents($path, $formatter->output());
        $zip = new \ZipArchive();
        $zip->open(str_replace('.kml', '.kmz', $path), \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE);
        $zip->addFile($path);
        $zip->close();
    }
    
    public function generate_kml() {
        $kml = new \formatter_kml($this->coordinate_set, "");
        $path = $this->get_file_loc() . '/track.kml';
        $path = root . get::trim_root($path);
        file_put_contents($path, $kml->output());
        $zip = new \ZipArchive();
        $zip->open(str_replace('.kml', '.kmz', $path), \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE);
        $zip->addFile($path);
        $zip->close();
    }    
   
    public function generate_kml_earth() {
        
        // TODO convert to c api;
        //        $kml = new kml();
        //
        //        $kml->set_folder_styles();
        //        $kml->set_gradient_styles(1);
        //        $kml->set_animation_styles(1);
        //
        //        $kml->get_kml_folder_open('Track ' . $this->id, 1, '', 1);
        //        $kml->get_kml_folder_open('Main Track', 1, 'radioFolder', 1);
        //
        //        $kml->get_kml_folder_open('Colour By Height', 1, 'hideChildren', 0);
        //        $kml->add($this->get_colour_by($this->min_ele, $this->maximum_ele, 'ele'));
        //        $kml->get_kml_folder_close();
        //
        //        $kml->get_kml_folder_open('Colour By Ground Speed', 0, 'hideChildren', 0);
        //        $kml->add($this->get_colour_by(0, $this->max_speed, 'speed'));
        //        $kml->get_kml_folder_close();
        //
        //        $kml->get_kml_folder_open('Colour By Climb', 0, 'hideChildren', 0);
        //        $kml->add($this->get_colour_by($this->min_cr, $this->max_cr, 'climbRate'));
        //        $kml->get_kml_folder_close();
        //
        //        $kml->get_kml_folder_open('Colour By Time', 0, 'hideChildren', 0);
        //        $kml->add($this->get_colour_by($this->coordinate_set->first()->timestamp(), $this->coordinate_set->last()
        //                                                                                                     ->timestamp(), 'time', 0));
        //        $kml->get_kml_folder_close();
        //
        //        $kml->get_kml_folder_close();
        //        $kml->get_kml_folder_open('Shadow', 1, 'radioFolder');
        //
        //        $kml->get_kml_folder_open('None', 0, 'hideChildren', 0);
        //        $kml->get_kml_folder_close();
        //
        //        $kml->get_kml_folder_open('Standard', 1, 'hideChildren', 0);
        //        if ($this->coordinate_set->count()) {
        //            $kml->add(kml::create_linestring('shadow', $this->coordinate_set, 'clampToGround'));
        //            $kml->get_kml_folder_close();
        //
        //            $kml->get_kml_folder_open('Extrude', 0, 'hideChildren', 0);
        //            $kml->add(kml::create_linestring('shadow', $this->coordinate_set, 'absolute', 1));
        //            $kml->get_kml_folder_close();
        //        }
        //
        //        $kml->get_kml_folder_close();
        //
        //        $kml->get_kml_folder_open('Colour By Time', 0);
        //        $kml->add($this->get_kml_time_aware_points();
        //        $kml->get_kml_folder_close();

        //        $kml->get_kml_folder_open('Task', 0, '', 0);
        //        $kml->get_kml_folder_open('Open Distance', 0, 'hideChildren', 0);
        //        $kml->add($this->od->get_kml_track('00D7FF', 'Open Distance'));
        //        $kml->get_kml_folder_close();
        //        $kml->get_kml_folder_open('Out And Return', 0, 'hideChildren', 0);
        //        $kml->add($this->or->get_kml_track('00FF00', 'Out And Return'));
        //        $kml->get_kml_folder_close();
        //        $kml->get_kml_folder_open('FAI Triangle', 0, 'hideChildren', 0);
        //        $kml->add($this->tr->get_kml_track('0000FF', 'FAI Triangle'));
        //        $kml->get_kml_folder_close();
        //        $kml->get_kml_folder_open('Flat Triangle', 0, 'hideChildren', 0);
        //        $kml->add($this->tr->get_kml_track('FF0066', 'Flat Triangle'));
        //        $kml->get_kml_folder_close();
        //        $kml->get_kml_folder_close();
        //
        //
        //        $kml->get_kml_folder_open('Animation', 0, 'hideChildren', 0);
        //        $kml->add($this->get_animation());
        //        $kml->get_kml_folder_close();
        //        $kml->get_kml_folder_close();
        //        $kml->compile(false, $this->get_file_loc() . '/track_earth.kml');
        //        return $kml;
        
    }
    
    public function generate_output_files() {
        $this->generate_kml();
        $this->generate_js();        
        //$this->generate_kml_earth();
        
    }
    
    public function generate_split_output_files() {
        $this->generate_kml_split();
        $this->generate_js();
    }
    
    public function get_animation() {
        
        // TODO use c api
        //        $xml = '';
        //        $tot = $this->coordinate_set->count();
        //        /** @var track_point $point */
        //        $point = $this->coordinate_set->get(0);
        //        for ($i = 0; $i < $tot - 1; $i++) {
        //            /** @var track_point $next_point */
        //            $next_point = $this->coordinate_set->get($i + 1);
        //            $bearing = floor($point->bearing / 5) * 5;
        //            $xml .= '<Placemark>';
        //            $xml .= '<styleUrl>#A' . $this->colour . $bearing . '</styleUrl>';
        //            $xml .= kml::get_timespan($point->timestamp(), $next_point->timestamp());
        //            $xml .= $point->get_kml_point();
        //            $xml .= '</Placemark>';
        //            $point = $next_point;
        //        }
        //        return $xml;
        
    }
    
    public function get_colour_by($min, $max, $value, $scale = 1) {
        
        // Todo add to c api
        //        $output = '';
        //        $var = ($max - $min ? $max - $min : 1);
        //        $last_level = floor(($this->coordinate_set->get(0)->$value - $min) * 16 / $var);
        //
        //        $coordinates = [];
        //        $current_level = 0;
        //        for ($i = 0; $i < $this->coordinate_set->count(); $i++) {
        //            $point = $this->coordinate_set->get($i);
        //            $coordinates[] = $point;
        //            $current_level = floor(($point->$value - $min) * 16 / $var);
        //            if ($current_level != $last_level && $current_level != 16) {
        //                $output .= kml::create_linestring('#S' . $last_level, new collection($coordinates));
        //                $coordinates = [];
        //                $coordinates[] = $point;
        //                $last_level = $current_level;
        //            }
        //        };
        //        if (!empty($coordinates)) {
        //            $output .= kml::create_linestring('#S' . $current_level, new collection($coordinates));
        //        }
        //        if ($scale) {
        //            $output .= kml::get_scale($min, $max);
        //        }
        //        return $output;
        
    }


    
    public function get_timestamp() {
        return strtotime($this->coordinate_set->date());
    }
    
    public function get_part_duration($i) {
        return $this->coordinate_set->part_duration($i);
    }
    
    public function get_part_length($i) {
        return $this->coordinate_set->part_length($i);
    }
    
    public function get_date($format = 'Y/m/d') {
        return date($format, $this->get_timestamp());
    }
    
    public function get_dim() {
        return $this->coordinate_set->has_height_data() ? 3 : 2;
    }
    
    public function get_duration($formatted = false) {
        if ($formatted) {
            return date('H:i:s', $this->coordinate_set->last()->timestamp() - $this->coordinate_set->first()->timestamp());
        } else {
            return $this->coordinate_set->last()->timestamp() - $this->coordinate_set->first()->timestamp();
        }
    }
    
    public function get_file_loc($id = null, $temp = null) {
        if (!isset($id)) {
            $id = $this->id;
        }
        if (!isset($temp)) {
            $temp = $this->temp;
        }
        return root . '/uploads/flight/' . ($temp ? 'temp/' : '') . $id;
    }
    
    public function get_kml_description() {
        return '';
    }
    
    public function get_kml_time_aware_points($col = false) {
        
        // TODO convert to c api
        //        $output = '';
        //        $tot = $this->coordinate_set->count();
        //        /** @var track_point $point */
        //        $point = $this->coordinate_set->get(0);
        //        for ($i = 0; $i < $tot - 1; $i++) {
        //            /** @var track_point $next_point */
        //            $next_point = $this->coordinate_set->get($i + 1);
        //            $current_level = floor(($point->timestamp() - $this->coordinate_set->first()
        //                                                                             ->timestamp()) * 16 / $this->get_duration());
        //            $output .= '<Placemark>';
        //            if (!$col) {
        //                $output .= '<styleUrl>#S' . $current_level . '</styleUrl>';
        //            } else {
        //                $output .= '<Style><LineStyle><color>FF' . $col . '</color><colorMode>normal</colorMode><width>2</width></LineStyle></Style>';
        //            }
        //            $output .= kml::get_timespan($point->timestamp(), $next_point->timestamp()) . '
        //          <LineString>
        //              <altitudeMode>absolute</altitudeMode>
        //              <coordinates>
        //                  ' . $point->get_kml_coordinate() . ' ' . $next_point->get_kml_coordinate() . '
        //              </coordinates>
        //          </LineString>
        //      </Placemark>';
        //            $point = $next_point;
        //        }
        //        return $output;
        
    }
    
    public function get_number_of_parts() {
        return $this->coordinate_set->part_count();
    }
    
    public function get_season() {
        $season = $this->get_date('Y');
        if ($this->get_date('n') >= 11) {
            $season++;
        }
        return $season;
    }
    
    public function get_time() {
        return $this->coordinate_set->last()->timestamp() - $this->coordinate_set->first()->timestamp();
    }
    
    public function is_winter() {
        $month = $this->get_date('n');
        return (in_array($month, [1, 2, 12]));
    }
    
    public function get_igc() {
        if ($this->id) {
            $loc_old = $this->get_file_loc() . '/track_log.igc';
            $loc = $this->get_file_loc() . '/track.igc';
            if (file_exists($loc_old)) {
                if (!(file_exists($loc_old) && copy($loc_old, $loc) && unlink($loc_old))) {
                    return false;
                }
            }
            if (file_exists($loc)) {
                return $loc;
            }
        }
        return false;
    }
    
    public function get_kmz() {
        return $this->get_file_loc() . '/track.kmz';
    }
    
    public function get_kmz_earth() {
        return $this->get_file_loc() . '/track_earth.kmz';
    }
    
    public function get_kmz_raw() {
        return $this->get_file_loc() . '/track_raw.kmz';
    }
    
    public function parse_IGC() {
        if ($file_path = $this->get_igc()) {
            $this->log->info("Flight Read", $this, 1, 1);
        } else {
            return false;
        }
        $parser = new \coordinate_set();
        $parser->parse_igc(file_get_contents($file_path));
        $parser->trim();
        $parser->repair();
        $parser->set_graph_values();
        $parser->set_ranges();
        $this->coordinate_set = $parser;
        if ($parser->count()) {
            return true;
        } else {
            return false;
        }
    }
    
    public function set_section($index) {
        $this->coordinate_set->set_section($index);
    }

    public function repair_track() {
        $previous = clone $this->track_points->first();
        /** @var track_point $track_point */
        $this->track_points->iterate(
            function (track_point $track_point) use (&$previous) {
                if ($this->has_height() && $track_point->ele == 0) {
                    $this->log->debug("Filled in trough  : 0 ele", $this);
                    $track_point->ele = $previous->ele;
                }
                if ($this->has_height() && $track_point->ele > $previous->ele + 500) {
                    $this->log->debug("Flattened peak  : {$previous->ele} -> $track_point->ele", $this);
                    $track_point->ele = $previous->ele;
                }
                $previous = clone $track_point;
            }
        );
    }

    public function set_from_session($id) {
        if (session::is_set('add_flight', $id)) {
            if (session::is_set('add_flight', $id, 'section')) {
                $this->set_section(session::get('add_flight', $id, 'section'));
            }
            $this->flight->od_score = session::get('add_flight', $id, 'od', 'distance');
            $this->flight->od_coordinates = session::get('add_flight', $id, 'od', 'coords');
            $this->flight->od_time = session::get('add_flight', $id, 'od', 'duration');
            if (session::is_set('add_flight', $id, 'or')) {
                $this->flight->or_score = session::get('add_flight', $id, 'or', 'distance');
                $this->flight->or_coordinates = session::get('add_flight', $id, 'or', 'coords');
                $this->flight->or_time = session::get('add_flight', $id, 'or', 'duration');
            }
            
            if (session::is_set('add_flight', $id, 'tr')) {
                $this->flight->tr_score = session::get('add_flight', $id, 'tr', 'distance');
                $this->flight->tr_coordinates = session::get('add_flight', $id, 'tr', 'coords');
                $this->flight->tr_time = session::get('add_flight', $id, 'tr', 'duration');
            }
            if (session::is_set('add_flight', $id, 'ft')) {
                $this->flight->ft_score = session::get('add_flight', $id, 'ft', 'distance');
                $this->flight->ft_coordinates = session::get('add_flight', $id, 'ft', 'coords');
                $this->flight->ft_time = session::get('add_flight', $id, 'ft', 'duration');
            }
            if (session::is_set('add_flight', $id, 'task')) {
                $this->flight->go_distance = session::get('add_flight', $id, 'task', 'distance');
                $this->flight->go_coordinates = session::get('add_flight', $id, 'task', 'coords');
                $this->flight->go_time = session::get('add_flight', $id, 'task', 'duration');
                $this->flight->go_type = session::get('add_flight', $id, 'task', 'type');
            }
            $this->flight->duration = $this->flight->ft_time = session::get('add_flight', $id, 'duration');
            return true;
        } else {
            return false;
        }
    }
    
    public function set_id($id) {
        $this->id = $id;
    }
    
    public function set_info() {
        if ($this->flight && $this->flight->fid) {
            $this->flight->lazy_load(['pid', 'gid', 'cid']);
            $this->pilot->do_retrieve_from_id(['name'], $this->flight->pid);
            $this->club->do_retrieve_from_id(['title'], $this->flight->cid);
            $this->glider->do_retrieve_from_id(['name'], $this->flight->gid);
        }
    }
    
    public function end_time($formatted = false) {
        if ($formatted) {
            return date('H:i:s', $this->coordinate_set->last()->timestamp());
        } else {
            return $this->coordinate_set->last()->timestamp();
        }
    }
    
    public function start_time($formatted = false) {
        if ($formatted) {
            return date('H:i:s', $this->coordinate_set->first()->timestamp());
        } else {
            return $this->coordinate_set->first()->timestamp();
        }
    }
    
    function track_flat_triangles($sub = false, $min_ratio = 0) {
        
        // TODO convert to c api
        //        $this->track_triangles($sub, $min_ratio, $type = 'ft');
        
    }


       public function generate_kml_comp($visible = true) {
        
        // Todo convert to c api
        //        $kml = new kml();
        //        $kml->get_kml_folder_open($this->name, $visible, 'hideChildren');
        //        $kml->add($this->get_comp_kml_description());
        //        $kml->add('
        //        <Style>
        //          <LineStyle>
        //            <color>FF' . get::kml_colour($this->colour) . '</color>
        //            <width>2</width>
        //          </LineStyle>
        //        </Style>'
        //        );
        //        $kml->add($this->get_kml_linestring());
        //        $kml->add('</Placemark>');
        //        $kml->get_kml_folder_close();
        //        return $kml->compile(true);
        
        
    }
    
    public function generate_kml_comp_earth($visible = true) {
        
        // TODO convert to c api
        //        $kml = new kml();
        //        $kml->get_kml_folder_open($this->name, $visible, 'hideChildren');
        //        $kml->add($this->get_kml_time_aware_points(get::kml_colour($this->colour)));
        //        $kml->get_kml_folder_close();
        //        return $kml->compile(true);
        
    }
    
    public function get_comp_kml_description() {
        
        // Todo add to c api
        //        return '
        //        <Placemark>
        //        <name>' . $this->name . '</name>
        //        <description><![CDATA[
        //        <pre>
        //Flight statistics
        //Pilot                ' . $this->name . '
        //Date                 ' . $this->get_date('d/m/Y') . '
        //Start/finish         ' . $this->start_time(true) . '-' . $this->end_time(true) . '
        //Duration             ' . $this->get_duration(true) . '
        //Max./min. height     ' . $this->maximum_ele . '/' . $this->maximum_ele . 'm
        //            </pre>]]>
        //        </description>';
        
    }
}
