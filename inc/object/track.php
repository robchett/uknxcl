<?php
/**
 * @property track_point_array track_points
 * @property track_part_array  track_parts
 * @property pilot pilot
 * @property club club
 * @property glider glider
 * @property flight parent_flight
 * @property coordinate_bound bounds
 */
class track {

    const FAI_RATIO = 0.275;
    const STAT_ALL = 0;
    const STAT_MIN = 1;
    const STAT_MAX = 2;
    const STAT_AVERAGE = 4;

    public static $number_of_points_to_use = 700;
    public $calc_od = 1;
    public $calc_or = 1;
    public $calc_tr = 1;
    public $calc_ft = 1;
    public $calculation_subset;
    public $colour = 0;
    public $date;
    public $day = false;
    public $distance_map;
    public $error = '';
    public $id;
    public $log = 0;
    public $log_file = "";
    public $maximum_alt = -1000000;
    public $maximum_cr = 0;
    public $maximum_distance_between_two_points;
    public $maximum_ele = -1000000;
    public $maximum_ele_t = 0;
    public $maximum_speed = 0;
    public $min_alt = 1000000;
    public $min_cr = 0;
    public $min_ele = 1000000;
    public $min_ele_t = 0;
    public $mon = false;
    public $parsed = false;
    public $raw = 1;
    public $source;
    public $temp = false;
    public $time;
    public $total_dist = 0;
    public $track_parts;
    public $track_points;
    public $year = false;
    private $calculation_subset_size = 0;
    private $club;
    private $generated_graph = false;
    private $glider;
    private $parent_flight;
    private $pilot;

    public function __construct() {
        $this->od = new task('Open Distance');
        $this->or = new out_and_return('Out and Return');
        $this->ft = new triangle('Flat Triangle');
        $this->tr = new triangle('Triangle');
        $this->parent_flight = new flight();
        $this->track_parts = new track_part_array();
        $this->track_points = new track_point_array();
        $this->parent_flight = new flight();
        $this->pilot = new pilot();
        $this->club = new club();
        $this->glider = new glider();
        $this->bounds = new coordinate_bound();
    }

    public static function move_temp_files($temp_id, $new_id) {
        $track = new track();
        $old_dir = $track->get_file_loc($temp_id, true);
        $new_dir = $track->get_file_loc($new_id, false);
        if (!file_exists($new_dir)) {
            mkdir($new_dir);
        }
        copy($old_dir . '/track.igc', $new_dir . '/track.igc');
        copy($old_dir . '/track_backup.igc', $new_dir . '/track_backup.igc');
    }

    static function split_igc($id, $start, $end) {
        copy(root . '/uploads/track/' . $id . '/track_backup.igc', root . '/uploads/track/' . $id . '/track.igc');
        $file = file(root . '/uploads/track/' . $id . '/track.igc');
        $outFile = fopen(root . '/uploads/track/' . $id . '/track.igc', 'w');
        $i = 0;
        $b_record_count = 0;
        while (isset ($file [$i])) {
            $record = $file [$i];
            if ($record [0] == 'B') {
                if ($b_record_count >= $start && $b_record_count <= $end)
                    fwrite($outFile, $record);
                $b_record_count++;
            } else
                fwrite($outFile, $record);
            $i++;
        }
    }

    /**
     * @param $field
     * @return stat_object
     * @throws Exception
     */
    public function get_stats($field) {
        $stat_object = new stat_object();
        if (!isset($this->track_points)) {
            throw new Exception('Trying to calculate stats on a track before it has been parsed.');
        }
        $stat_object->min = $this->track_points->first()->$field;
        $stat_object->min_point = $this->track_points->first();
        foreach ($this->track_points as $point) {
            if ($point->$field < $stat_object->min) {
                $stat_object->min = $point->$field;
                $stat_object->min_point = $point;
            }
            if ($point->$field > $stat_object->max) {
                $stat_object->max = $point->$field;
                $stat_object->max_point = $point;
            }
            $stat_object->total += $point->$field;
        }
        $stat_object->average = $stat_object->total / $this->track_points->count();
        return $stat_object;
    }

    public function calculate() {
        db::query('SET wait_timeout=1200');
        set_time_limit(0);
        $this->pre_calc();
        $this->get_dist_map();
        $use_rough_calculations = self::$number_of_points_to_use > $this->track_points->count();
        if ($this->calc_od) {
            $this->track_open_distance_3tp($use_rough_calculations);
            if (isset($this->od->waypoints)) {
                $this->od->waypoints->spherical = false;
                unset($this->od->distance);
                $this->console("Open Distance Calculated, Dist:{$this->od->get_distance()} Cords={$this->od->get_coordinates()}", $this);
            }
        }
        if ($this->calc_or) {
            $this->track_out_and_return($use_rough_calculations);
            if (isset($this->or->waypoints)) {
                $this->or->waypoints->spherical = false;
                unset($this->or->distance);
                $this->console("Out and Return Calculated, Dist:{$this->or->get_distance()} Cords={$this->or->get_coordinates()}");
            }
        }
        if ($this->calc_ft) {
            $this->track_flat_triangles($use_rough_calculations);
            if (isset($this->ft->waypoints)) {
                $this->ft->waypoints->spherical = false;
                unset($this->ft->distance);
                $this->console("Flat Triangle Calculated, Dist:{$this->ft->get_distance()} Cords={$this->ft->get_coordinates()}", $this);
            }
        }
        if ($this->calc_tr) {
            $this->track_triangles($use_rough_calculations);
            if (isset($this->tr->waypoints)) {
                $this->tr->waypoints->spherical = false;
                unset($this->tr->distance);
                $this->console("Triangle Calculated, Dist:{$this->tr->get_distance()} Cords={$this->tr->get_coordinates()}", $this);
            }
        }
        $this->set_info();
    }

    public function check_date() {
        $current_time = time();
        $closure_time = $current_time - (31 * 24 * 60 * 60);
        if ($this->date >= $closure_time && $this->date <= $current_time) {
            $this->console("Date is within 1 month");
            return true;
        } else {
            $this->console("Date is outside of 1 month");
            return false;
        }
    }

    public function cleanup() {
        unset($this->track_points);
        unset($this->track_parts);
        unset($this->or);
        unset($this->od);
        unset($this->tr);
        unset($this->c);
    }

    public function console($str) {
        $this->log_file .= "$str\r\n";
    }

    public function create_from_upload() {
        if (isset($_FILES ["file"] ["tmp_name"])) {
            $dir = $this->get_file_loc();
            if (!file_exists($dir)) {
                mkdir($dir);
            } else {
                $files = glob($dir . '*', GLOB_MARK);
                foreach ($files as $file) {
                    unlink($file);
                }
            }
            move_uploaded_file($_FILES ["file"] ["tmp_name"], $dir . '/track.igc');
            copy($dir . '/track.igc', $dir . '/track_backup.igc');
        }
    }

    public function dist_test() {
        $trackpoint1 = new track_point();
        $trackpoint1->sin_lat = sin(M_PI * 52 / 180);
        $trackpoint1->cos_lat = cos(M_PI * 52 / 180);
        $trackpoint1->lonRad = M_PI * 0 / 180;
        $trackpoint2 = new track_point();
        $trackpoint2->sin_lat = sin(M_PI * 53 / 180);
        $trackpoint2->cos_lat = cos(M_PI * 53 / 180);
        $trackpoint2->lonRad = M_PI * 1 / 180;

        echo $trackpoint1->get_dist_to($trackpoint2);
    }

    public function enable_logging($bool) {
        $this->log = $bool;
    }

    public function enable_raw($bool) {
        $this->raw = $bool;
    }

    public function end_time($formatted = false) {
        if ($formatted) {
            return date('H:i:s', $this->track_points->last()->time);
        } else {
            return $this->track_points->last()->time;
        }
    }

    public function furthest_between($start, $end) {
        $max = $start;
        for ($x = $start; $x < $end; $x++) {
            if ($this->distance_map[$start][$x] > $this->distance_map[$max][$start])
                $max = $x;
            else $x += (int) (($this->distance_map[$max][$start] - $this->distance_map[$start][$x]) / $this->maximum_distance_between_two_points);
        }
        return $max;
    }

    public function generate(flight $flight) {
        $this->id = $flight->fid;
        $this->parent_flight = $flight;
        if ($this->parse_IGC()) {
            $this->calculate();
            $this->generate_output_files();

            $this->parent_flight->duration = $this->get_time();

            $this->parent_flight->od_score = $this->od->get_distance();
            $this->parent_flight->od_time = $this->od->get_time();
            $this->parent_flight->od_coordinates = $this->od->get_coordinates();

            $this->parent_flight->or_score = $this->or->get_distance();
            $this->parent_flight->or_time = $this->or->get_time();
            $this->parent_flight->or_coordinates = $this->or->get_coordinates();

            $this->parent_flight->tr_score = $this->tr->get_distance();
            $this->parent_flight->tr_time = $this->tr->get_time();
            $this->parent_flight->tr_coordinates = $this->tr->get_coordinates();

            $this->parent_flight->ft_score = $this->ft->get_distance();
            $this->parent_flight->ft_time = $this->ft->get_time();
            $this->parent_flight->ft_coordinates = $this->ft->get_coordinates();
            return true;
        } else {
            return false;
        }
    }

    public function generate_js() {
        $track = new stdClass();
        $track->id = $this->id;
        $track->StartT = 0;
        $track->EndT = ($this->get_duration() ? $this->get_duration() : 0);
        if ($this->od->waypoints) {
            $track->od_score = $this->od->get_distance();
            $track->od_time = $this->od->get_time();
            $track->or_score = $this->or->get_distance();
            $track->or_time = $this->or->get_time();
            $track->tr_score = $this->tr->get_distance();
            $track->tr_time = $this->tr->get_time();
            $track->ft_score = $this->ft->get_distance();
            $track->ft_time = $this->ft->get_time();
        }

        $track_inner = new stdClass();
        $track_inner->drawGraph = 1;
        $track_inner->pilot = isset($this->pilot->name) ? $this->pilot->name : 'Unknown';
        $track_inner->colour = "FF0000";
        $track_inner->maxEle = $this->maximum_ele;
        $track_inner->minEle = $this->min_ele;
        $track_inner->maximum_cr = $this->maximum_cr;
        $track_inner->min_cr = $this->min_cr;
        $track_inner->maximum_speed = $this->maximum_speed;
        $track_inner->min_speed = 0;
        $track_inner->total_dist = $this->total_dist;
        $track_inner->av_speed = (isset($this->average_speed_over_track) ? $this->average_speed_over_track : 0);
        $track_inner->coords = array();
        foreach ($this->track_points as $a) {
            $time = $a->time - $this->track_points->first()->time;
            $track_inner->coords[] = array($a->lat, $a->lon, $a->ele, $time, $a->climbRate, $a->speed, $a->bearing);
        }
        $track->track = array($track_inner);
        $track->bounds = $this->bounds->get_js();
        $kml = $this->generate_kml();
        $track->html = '<div class="kmltree" data-post=\'{"id":' . $this->id . '}\'>' . $kml->get_html() . '</div>';
        $kml = $this->generate_kml_earth();
        $track->html_earth = '<div class="kmltree" data-post=\'{"id":' . $this->id . '}\'>' . $kml->get_html() . '</div>';

        fwrite(fopen($this->get_file_loc() . '/track.js', 'w'), json_encode($track));
        fwrite(fopen($this->get_file_loc() . '/info.txt', 'w'), $this->log_file);
    }

    public function  generate_kml($external = false) {
        $kml = new kml();
        $kml->get_kml_folder_open('Flight ' . $this->id);
        if (!$external) {
            $kml->set_gradient_styles();
        }
        $kml->add($this->get_kml_description());
        if (!$external) {
            $kml->get_kml_folder_open('Track ' . $this->id, 1, '', 1);
            $kml->add($this->get_meta_linestring());
            $kml->get_kml_folder_close();
            $kml->get_kml_folder_open('Task', 1, '', 0);
            $kml->get_kml_folder_open('Open Distance', 1, 'hideChildren', 0);
            $kml->add($this->od->get_kml_track('FF0000', 'Open Distance'));
            $kml->get_kml_folder_close();
            if ($this->or->distance) {
                $kml->get_kml_folder_open('Out And Return', 1, 'hideChildren', 0);
                $kml->add($this->or->get_kml_track('00FF00', 'Out And Return'));
                $kml->get_kml_folder_close();
            }
            if ($this->tr->distance) {
                $kml->get_kml_folder_open('FAI Triangle', 1, 'hideChildren', 0);
                $kml->add($this->tr->get_kml_track('0000FF', 'FAI Triangle'));
                $kml->get_kml_folder_close();
            }
            if ($this->ft->distance) {
                $kml->get_kml_folder_open('Flat Triangle', 1, 'hideChildren', 0);
                $kml->add($this->tr->get_kml_track('FF0066', 'Flat Triangle'));
                $kml->get_kml_folder_close();
            }
            $kml->get_kml_folder_close();
        }
        $kml->get_kml_folder_close();
        if (0) {
            $kml->add($this->get_colour_by_height());
        }
        if ($external) {
            $kml->add($this->get_kml_time_aware_points());
        }
        if (!$external) {
            $kml->compile(false, $this->get_file_loc() . '/track.kml');
        }
        return $kml;
    }

    public function generate_kml_comp($visible = true) {
        $kml = new kml();
        $kml->get_kml_folder_open($this->name, $visible, 'hideChildren');
        $kml->add($this->get_comp_kml_description());
        $kml->add('
        <Style>
          <LineStyle>
            <color>FF' . get::kml_colour($this->colour) . '</color>
            <width>2</width>
          </LineStyle>
        </Style>'
        );
        $kml->add($this->get_kml_linestring());
        $kml->add('</Placemark>');
        $kml->get_kml_folder_close();
        return $kml->compile(true);

    }

    public function generate_kml_comp_earth($visible = true) {
        $kml = new kml();
        $kml->get_kml_folder_open($this->name, $visible, 'hideChildren');
        $kml->add($this->get_kml_time_aware_points(get::kml_colour($this->colour)));
        $kml->get_kml_folder_close();
        return $kml->compile(true);
    }

    public function generate_kml_earth() {
        $kml = new kml();

        $kml->set_folder_styles();
        $kml->set_gradient_styles(1);
        $kml->set_animation_styles(1);

        $kml->get_kml_folder_open('Track ' . $this->id, 1, '', 1);
        $kml->get_kml_folder_open('Main Track', 1, 'radioFolder', 1);

        $kml->get_kml_folder_open('Colour By Height', 1, 'hideChildren', 0);
        $kml->add($this->get_colour_by($this->min_ele, $this->maximum_ele, 'ele'));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_open('Colour By Ground Speed', 0, 'hideChildren', 0);
        $kml->add($this->get_colour_by(0, $this->maximum_speed, 'speed'));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_open('Colour By Climb', 0, 'hideChildren', 0);
        $kml->add($this->get_colour_by($this->min_cr, $this->maximum_cr, 'climbRate'));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_open('Colour By Time', 0, 'hideChildren', 0);
        $kml->add($this->get_colour_by($this->track_points->first()->time, $this->track_points->last()->time, 'time', 0));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_close();
        $kml->get_kml_folder_open('Shadow', 1, 'radioFolder');

        $kml->get_kml_folder_open('None', 0, 'hideChildren', 0);
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_open('Standard', 1, 'hideChildren', 0);
        $kml->add(kml::create_linestring('shadow', $this->track_points->subset(), 'clampToGround'));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_open('Extrude', 0, 'hideChildren', 0);
        $kml->add(kml::create_linestring('shadow', $this->track_points->subset(), 'absolute', 1));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_close();

        /*      $kml->get_kml_folder_open('Colour By Time', 0);
                $kml->add($this->get_kml_time_aware_points();
                $kml->get_kml_folder_close();*/

        $kml->get_kml_folder_open('Task', 0, '', 0);
        $kml->get_kml_folder_open('Open Distance', 0, 'hideChildren', 0);
        $kml->add($this->od->get_kml_track('FF0000', 'Open Distance'));
        $kml->get_kml_folder_close();
        $kml->get_kml_folder_open('Out And Return', 0, 'hideChildren', 0);
        $kml->add($this->or->get_kml_track('00FF00', 'Out And Return'));
        $kml->get_kml_folder_close();
        $kml->get_kml_folder_open('FAI Triangle', 0, 'hideChildren', 0);
        $kml->add($this->tr->get_kml_track('0000FF', 'FAI Triangle'));
        $kml->get_kml_folder_close();
        $kml->get_kml_folder_open('Flat Triangle', 0, 'hideChildren', 0);
        $kml->add($this->tr->get_kml_track('FF0066', 'Flat Triangle'));
        $kml->get_kml_folder_close();
        $kml->get_kml_folder_close();


        $kml->get_kml_folder_open('Animation', 0, 'hideChildren', 0);
        $kml->add($this->get_animation());
        $kml->get_kml_folder_close();
        $kml->get_kml_folder_close();
        $kml->compile(false, $this->get_file_loc() . '/track_earth.kml');
        return $kml;
    }

    public function generate_output_files() {
        $this->generate_js();
        $this->generate_kml();
        $this->generate_kml_earth();
    }

    public function getTime($time) {
        $time -= mktime(0, 0, 0);
        return date('H:i:s', $time);
    }

    public function get_animation() {
        $xml = '';
        $tot = $this->track_points->count();
        for ($i = 0; $i < $tot - 1; $i++) {
            $bearing = floor($this->track_points[$i]->bearing / 5) * 5;
            $xml .= '<Placemark>';
            $xml .= '<styleUrl>#A' . $this->colour . $bearing . '</styleUrl>';
            $xml .= kml::get_timespan($this->track_points[$i]->time, $this->track_points[$i + 1]->time);
            $xml .= $this->track_points[$i]->get_kml_point();
            $xml .= '</Placemark>';
        }
        return $xml;
    }

    public function get_colour_by($min, $max, $value, $scale = 1) {
        $this->get_graph_values();
        $output = '';
        $var = ($max - $min ? $max - $min : 1);
        $last_level = floor(($this->track_points[0]->$value - $min) * 16 / $var);

        $coords = array();
        foreach ($this->track_points as $point) {
            $coords[] = $point;
            $current_level = floor(($point->$value - $min) * 16 / $var);
            if ($current_level != $last_level && $current_level != 16) {
                $output .= kml::create_linestring('#S' . $last_level, $coords);
                $coords = array();
                $coords[] = $point;
                $last_level = $current_level;
            }
        }
        if (!empty($coords))
            $output .= kml::create_linestring('#S' . $current_level, $coords);
        if ($scale)
            $output .= kml::get_scale($min, $max);
        return $output;
    }

    public function get_comp_kml_description() {
        return '
        <Placemark>
        <name>' . $this->name . '</name>
        <description><![CDATA[
        <pre>
Flight statistics
Pilot                ' . $this->name . '
Date                 ' . $this->get_date('d/m/Y') . '
Start/finish         ' . $this->start_time(true) . '-' . $this->end_time(true) . '
Duration             ' . $this->get_duration(true) . '
Max./min. height     ' . $this->maximum_ele . '/' . $this->maximum_ele . 'm
            </pre>]]>
        </description>';
    }

    public function get_date($format = 'Y/m/d') {
        return date($format, $this->date);
    }

    public function get_dim() {
        return (($this->maximum_alt != $this->min_alt) || ($this->maximum_ele != $this->min_ele)) ? 3 : 2;
    }

    public function get_dist_map() {
        $this->distance_map = array();
        foreach ($this->calculation_subset as $key => $point) {
            if (is_null($point)) {
                continue;
            }
            for ($key2 = $key; $key2 < $this->calculation_subset_size; $key2++) {
                $y = (int) ($point->get_dist_to($this->calculation_subset[$key2]) * 1000);
                $this->distance_map[$key][$key2] = $y;
                $this->distance_map[$key2][$key] = $y;
                if ($y > $this->maximum_distance_between_two_points) $this->maximum_distance_between_two_points = $y;
            }
        }
        for ($i = 0; $i < $this->calculation_subset_size - 1; $i++) {
            if ($this->distance_map[$i][$i + 1] > $this->maximum_distance_between_two_points) $this->maximum_distance_between_two_points = $this->distance_map[$i][$i + 1];
        }
        $this->console("Distances between points calculated");
    }

    public function get_dist_remap($indexes) {
        $this->calculation_subset = array();
        foreach ($indexes as $index) {
            $index = $this->c_backup[$index];
            $start = isset($index->before) ? $index->before : $index->id;
            $end = isset($index->after) ? $index->after : $index->id;
            if ($end - $start < 50) {
                for ($i = $start; $i <= $end; $i++) {
                    $this->calculation_subset[] = $this->track_points[$i];
                }
            } else {
                $gap = ceil(($end - $start) / 50);
                for ($i = $start; $i <= $end; $i += $gap) {
                    if (isset($this->track_points[$i])) {
                        $this->calculation_subset[] = $this->track_points[$i];
                    }
                }
            }
        }
        $this->calculation_subset_size = count($this->calculation_subset);
        $this->get_dist_map();
        return count($this->calculation_subset);
    }

    public function get_duration($formatted = false) {
        if ($formatted) {
            return date('H:i:s', $this->track_points->last()->time - $this->track_points->first()->time);
        } else {
            return $this->track_points->last()->time - $this->track_points->first()->time;
        }
    }

    public function get_file_loc($id = null, $temp = null) {
        if (!isset($id)) {
            $id = $this->id;
        }
        if (!isset($temp)) {
            $temp = $this->temp;
        }
        return root . '/uploads/track/' . ($temp ? 'temp/' : '') . $id;
    }

    public function get_graph_values() {
        if (!$this->generated_graph) {
            $previous = $this->track_points->first();
            foreach ($this->track_points as $key => $track_point) {
                $next_piont = (isset($this->track_points[$key + 1]) ? $this->track_points[$key + 1] : $track_point);
                if ($this->has_height() && $next_piont->time - $previous->time) {
                    $track_point->climbRate = ($next_piont->ele - $previous->ele) / ($next_piont->time - $previous->time);
                } else
                    $track_point->climbRate = 0;
                if ($previous->time !== $next_piont->time) {
                    $x = $next_piont->get_dist_to($previous);
                    $track_point->speed = round(($x * 1000) / ($next_piont->time - $previous->time), 2);
                    $this->total_dist += $x;
                } else
                    $track_point->speed = 0;
                if ($previous->time !== $next_piont->time) {
                    $y = sin($next_piont->lonRad - $previous->lonRad) * $next_piont->cos_lat;
                    $x = $previous->cos_lat * $next_piont->sin_lat - $previous->sin_lat * $next_piont->cos_lat * cos($next_piont->lonRad - $previous->lonRad);
                    $track_point->bearing = atan2($y, $x) * 180 / M_PI;
                } else {
                    $track_point->bearing = 0;
                }
                if ($track_point->bearing < 0)
                    $track_point->bearing += 360;
                $previous = $track_point;

            }
        }
        $this->generated_graph = true;
    }

    public function get_kml_description() {
        return '
      <name>Flight ' . $this->id . '</name>
      <description>
          <![CDATA[
            <pre>
Flight statistics
Flight #             ' . $this->id . '
Pilot                ' . (isset($this->pilot->name) ? $this->pilot->name : '') . '
Club                 ' . (isset($this->club->name) ? $this->club->name : '') . '
Glider               ' . (isset($this->glider->name) ? $this->glider->name : '') . '
Date                 ' . $this->get_date('d/m/Y') . '
Start/finish         ' . $this->start_time(true) . ' / ' . $this->end_time(true) . '
Duration             ' . $this->get_duration(true) . '
Max./min. height     ' . $this->maximum_ele . ' / ' . $this->min_ele . 'm
OD Score / Time      ' . $this->od->get_distance() . ' / ' . $this->od->get_formatted_time() . '
OR Score / Time      ' . $this->or->get_distance() . ' / ' . $this->or->get_formatted_time() . '
TR Score / Time      ' . $this->tr->get_distance() . ' / ' . $this->tr->get_formatted_time() . '
            </pre>
          ]]>
      </description>';
    }

    public function get_kml_linestring($start = 0, $end = 0, $altitude = 'absolute', $extrude = 0) {
        $num = count($this->track_points);
        if ($end == 0) $end = $num - 1;
        $cnt = 0;
        if (!$start)
            $start = $this->track_points->first_index();
        $output = '';
        $output .= '
    <LineString>
        <altitudeMode>' . $altitude . '</altitudeMode>
        <extrude>' . $extrude . '</extrude>
        <coordinates>';
        for ($i = $start; $i < $num; $i++, $cnt++) {
            if ($cnt == 5) {
                $output .= "\n";
                $cnt = 0;
            }
            $output .= $this->track_points[$i]->get_kml_coordinate();
        }
        $output .= '
        </coordinates>
  </LineString>';
        return $output;
    }

    public function get_kml_time_aware_points($col = false) {
        $output = '';
        $tot = $this->track_points->count();
        for ($i = 0; $i < $tot - 1; $i++) {
            $current_level = floor(($this->track_points[$i]->time - $this->track_points->first()->time) * 16 / $this->get_duration());
            $output .= '<Placemark>';
            if (!$col)
                $output .= '<styleUrl>#S' . $current_level . '</styleUrl>';
            else
                $output .= '<Style><LineStyle><color>FF' . $col . '</color><colorMode>normal</colorMode><width>2</width></LineStyle></Style>';
            $output .= kml::get_timespan($this->track_points[$i]->time, $this->track_points[$i + 1]->time) . '
			<LineString>
				<altitudeMode>absolute</altitudeMode>
				<coordinates>
					' . $this->track_points[$i]->get_kml_coordinate() . ' ' . $this->track_points[$i + 1]->get_kml_coordinate() . '
				</coordinates>
			</LineString>
		</Placemark>';
        }
        return $output;
    }

    public function get_limits() {
        $start_time = $this->track_points->first()->time;
        foreach ($this->track_points as $track_point) {
            // Compare heights with max/min
            if ($track_point->ele > $this->maximum_ele) {
                $this->maximum_ele = $track_point->ele;
                $this->maximum_ele_t = $track_point->time - $start_time;
            }
            if ($track_point->ele < $this->min_ele) {
                $this->min_ele = $track_point->ele;
                $this->min_ele_t = $track_point->time - $start_time;
            }
            if ($track_point->alt > $this->maximum_alt) {
                $this->maximum_alt = $track_point->alt;
                $this->maximum_alt_t = $track_point->time - $start_time;
            }
            if ($track_point->alt < $this->min_alt) {
                $this->min_alt = $track_point->alt;
                $this->min_alt_t = $track_point->time - $start_time;
            }
            if ($track_point->climbRate < $this->min_cr) {
                $this->min_cr = $track_point->climbRate;
            }
            if ($track_point->climbRate > $this->maximum_cr) {
                $this->maximum_cr = $track_point->climbRate;
            }
            if ($track_point->speed > $this->maximum_speed) {
                $this->maximum_speed = $track_point->speed;
            }
            $this->bounds->add_coordinate_to_bounds($track_point->lat, $track_point->lon);

        }
    }

    public function get_list($indexes) {
        $list = array();
        foreach ($indexes as $index) {
            $list[] = $this->calculation_subset[$index];
        }
        return $list;
    }

    public function get_number_of_parts() {
        return count($this->track_parts);
    }

    public function get_season() {
        $season = $this->year;
        if ($this->mon >= 11)
            $season++;
        return $season;
    }

    public function get_time() {
        return $this->track_points->last()->time - $this->track_points->first()->time;
    }

    public function get_time_meta_data($start = 0, $end = 0) {
        $cnt = 0;
        $num = count($this->track_points);
        if ($end == 0) {
            $end = $num - 1;
        }
        $output = "
      <Metadata src='UKNXCL' v='0.9' type='track'>
        <SecondsFromTimeOfFirstPoint>\n";
        for ($i = $start; $i < $num; $i++, $cnt++) {
            if ($cnt == 10) {
                $output .= "\n";
                $cnt = 0;
            }
            $output .= $this->track_points[$i]->time - $this->track_points->first()->time . " ";
        }
        $output .= '
        </SecondsFromTimeOfFirstPoint>
      </Metadata>';
        return $output;

    }

    public function has_height() {
        if (!isset($this->has_height)) {
            foreach ($this->track_points as $track_point) {
                if ($track_point->ele != 0) {
                    $this->has_height = true;
                    return true;
                }
            }
            $this->has_height = false;
        }
        return $this->has_height;
    }

    public function is_winter() {
        return ($this->mon == 1 || $this->mon == 2 || $this->mon == 12);
    }

    public function match_b_record($p) {
        $track_point = new track_point ();
        $pos = 1;
        $track_point->time = mktime(substr($p, $pos, 2), substr($p, $pos + 2, 2), substr($p, $pos + 4, 2));
        $pos += 6;
        $lat = round(substr($p, $pos, 2) + substr($p, $pos + 2, 5) / 60000, 6);
        $track_point->lat = (substr($p, $pos + 7, 1) == 'N') ? $lat : -1 * $lat;
        $pos += 8;
        $lon = round(substr($p, $pos, 3) + (substr($p, $pos + 3, 5) / 60000), 6);
        $track_point->lon = (substr($p, $pos + 8, 1) == 'E') ? $lon : -1 * $lon;
        $pos += 9;
        $track_point->val = (int) substr($p, $pos, 1);
        $pos += 1;
        $track_point->alt = (int) substr($p, $pos, 5);
        $pos += 5;
        $track_point->ele = (int) substr($p, $pos, 5);

        $track_point->sin_lat = sin(M_PI * $track_point->lat / 180);
        $track_point->cos_lat = cos(M_PI * $track_point->lat / 180);
        $track_point->lonRad = M_PI * $track_point->lon / 180;
        $track_point->latRad = M_PI * $track_point->lat / 180;
        if ($this->track_points->count() == 0) {
            $this->track_points[] = $track_point;
            $this->track_parts[] = new track_part($track_point, 0);
            return;
        }
        $last_point = $this->track_points->last();
        if ($track_point->lat == $last_point->lat && $track_point->lon == $last_point->lon) {
            return;
        }
        $track_point->id = $this->track_points->count() - 1;
        $this->track_points[] = $track_point;
        if ($track_point->time - $last_point->time > 60 || $track_point->time - $last_point->time < 0) {
            $this->track_parts->last()->finish($last_point, $this->track_points->count() - 2);
            $this->track_parts[] = new track_part($track_point, $this->track_points->count() - 1);
        }
    }

    public function match_h_record($p) {
        if (substr($p, 0, 5) == "HFDTE") {
            $this->day = substr($p, 5, 2);
            $this->mon = substr($p, 7, 2);
            $this->year = "20" . substr($p, 9, 2);
            $this->date = strtotime($this->year . '/' . $this->mon . '/' . $this->day);
            $this->console("Date recorded as : " . $this->get_date('d/m/Y'), $this, 1, 1);
        }
    }

    public function maximum_bound_index($array, $from, $to) {
        $index = $from;
        for ($i = $from; $i < $to; ++$i) {
            if ($array[$index] < $array[$i]) {
                $index = $i;
            } else {
                $i += (int) (($array[$index] - $array[$i]) / $this->maximum_distance_between_two_points);
            }

        }
        return array($array[$index], $index);
    }

    public function mod60000($a) {
        return round($a / 60000, 6);
    }

    public function parse_IGC() {
        if ($this->id) {
            $loc = $this->get_file_loc() . '/track.igc';
            if (!file_exists($loc)) {
                $loc_old = $this->get_file_loc() . '/track_log.igc';
                if (!(file_exists($loc_old) && copy($loc_old, $loc) && unlink($loc_old))) {
                    return false;
                }
            }
            $file = file($loc);
            $this->console("Flight Read", $this, 1, 1);
        } elseif ($this->source) {
            $file = file($this->source);
        } else {
            return false;
        }
        foreach ($file as $line) {
            switch (substr($line, 0, 1)) {
                case 'B' :
                    $this->match_b_record($line);
                    break;
                case 'H' :
                    $this->match_h_record($line);
                    break;
            }
        }
        $this->track_parts->last()->finish($this->track_points->last(), $this->track_points->count() - 1);
        if ($this->track_points->count()) {
            $this->parsed = true;
            return true;
        } else {
            return false;
        }
    }

    public function pre_calc() {
        $this->console('Pre Calculation Setup:');
        $this->trim();
        $this->repair_track();
        $this->get_graph_values();
        $this->get_limits();
        $this->calculation_subset = array();
        if ($this->track_points->count() < self::$number_of_points_to_use) {
            $this->calculation_subset = $this->track_points;
        } else {
            $no = ($this->track_points->count() > self::$number_of_points_to_use) ? self::$number_of_points_to_use : $this->track_points->count();
            $average_distance = $this->total_dist / $no;
            $dist = 0;
            $cnt = 0;
            $this->calculation_subset[] = $this->track_points->first();
            $this->calculation_subset[0]->before = 0;
            foreach ($this->track_points as $key => $point) {
                if (isset($this->track_points[$key + 1])) {
                    $dist += $point->get_dist_to($this->track_points[$key + 1]);
                    if ($dist > $average_distance) {
                        $cnt++;
                        $this->calculation_subset[$cnt] = $point;
                        $this->calculation_subset[$cnt]->before = $this->calculation_subset[$cnt - 1]->id;
                        $this->calculation_subset[$cnt - 1]->after = $this->calculation_subset[$cnt]->id;
                        $dist = 0;
                    }
                } else {
                    $cnt++;
                    $this->calculation_subset[$cnt] = $point;
                    $this->calculation_subset[$cnt]->before = $this->calculation_subset[$cnt - 1]->id;
                    $this->calculation_subset[$cnt - 1]->after = $point->id;
                }
            }
        }
        $this->calculation_subset_size = count($this->calculation_subset);
        $this->console('-> Using ' . $this->calculation_subset_size . ' Track Points');
    }


    public function repair_track() {
        $previous = $this->track_points->first();
        foreach ($this->track_points as $track_point) {
            if ($this->has_height() && $track_point->ele == 0) {
                //$this->console("Filled in trough  : 0 ele", $this);
                $track_point->ele = $previous->ele;
            }
            if ($this->has_height() && $track_point->ele > $previous->ele + 500) {
                //$this->console("Flattened peak  : {$this->track_points->last()->ele} -> $track_point->ele", $this);
                $track_point->ele = $previous->ele;
            }
            $previous = $track_point;
        }
    }

    public function set_from_session(flight $flight, $id) {
        if (isset($_SESSION['add_flight'][$id])) {
            $this->truncate($_SESSION['add_flight'][$id]['start'], $_SESSION['add_flight'][$id]['end']);
            $this->od->distance = $_SESSION['add_flight'][$id]['od']['distance'];
            $this->od->coordinates = $_SESSION['add_flight'][$id]['od']['coords'];
            $this->od->timestamp = $_SESSION['add_flight'][$id]['od']['duration'];
            $this->od->get_waypoints_from_os();
            $this->od->waypoints->spherical = false;

            $this->or->distance = $_SESSION['add_flight'][$id]['or']['distance'];
            $this->or->coordinates = $_SESSION['add_flight'][$id]['or']['coords'];
            $this->or->timestamp = $_SESSION['add_flight'][$id]['or']['duration'];
            $this->or->get_waypoints_from_os();
            $this->or->waypoints->spherical = false;

            $this->tr->distance = $_SESSION['add_flight'][$id]['tr']['distance'];
            $this->tr->coordinates = $_SESSION['add_flight'][$id]['tr']['coords'];
            $this->tr->timestamp = $_SESSION['add_flight'][$id]['tr']['duration'];
            $this->tr->get_waypoints_from_os();
            $this->tr->waypoints->spherical = false;

            $this->ft->distance = $_SESSION['add_flight'][$id]['ft']['distance'];
            $this->ft->coordinates = $_SESSION['add_flight'][$id]['ft']['coords'];
            $this->ft->timestamp = $_SESSION['add_flight'][$id]['ft']['duration'];
            $this->ft->get_waypoints_from_os();
            $this->ft->waypoints->spherical = false;

            if (isset($_SESSION['add_flight'][$id]['task'])) {
                $this->task = new task();
                $this->task->distance = $_SESSION['add_flight'][$id]['task']['distance'];
                $this->task->coordinates = $_SESSION['add_flight'][$id]['task']['coords'];
                $this->task->timestamp = $_SESSION['add_flight'][$id]['task']['duration'];
                $this->task->type = $_SESSION['add_flight'][$id]['task']['type'];
            }
            $this->parent_flight = $flight;
            $this->parent_flight->duration = $this->get_time();
            $this->parent_flight->od_score = $this->od->get_distance();
            $this->parent_flight->od_time = $this->od->get_time();
            $this->parent_flight->od_coordinates = $this->od->get_coordinates();
            $this->parent_flight->or_score = $this->or->get_distance();
            $this->parent_flight->or_time = $this->or->get_time();
            $this->parent_flight->or_coordinates = $this->or->get_coordinates();
            $this->parent_flight->tr_score = $this->tr->get_distance();
            $this->parent_flight->tr_time = $this->tr->get_time();
            $this->parent_flight->tr_coordinates = $this->tr->get_coordinates();
            return true;
        } else {
            return false;
        }

    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function set_info() {
        if (isset($this->parent_flight->fid) && $this->parent_flight->fid) {
            $this->parent_flight->lazy_load(array('pid', 'gid', 'cid'));
            $this->pilot->do_retrieve_from_id(array('name'), $this->parent_flight->pid);
            $this->club->do_retrieve_from_id(array('title'), $this->parent_flight->cid);
            $this->glider->do_retrieve_from_id(array('name'), $this->parent_flight->gid);
        }
    }

    public function set_source($id) {
        $this->source = $id;
    }

    public function set_task($coordinates) {
        $task = new task();
        $task->waypoints = new track_point_array();
        $points = explode(';', $coordinates);
        foreach ($points as &$a) {
            $point = new track_point();
            $lat_lon = geometry::os_to_lat_long($a);
            $point->lat = $lat_lon[0];
            $point->lon = $lat_lon[1];
            $point->sin_lat = sin($point->lat * M_PI / 180);
            $point->cos_lat = cos($point->lat * M_PI / 180);
            $point->lonRad = $point->lon * M_PI / 180;
            $task->waypoints[] = $point;
        }

        if ($task->waypoints->count() == 3 && $task->waypoints[0]->get_dist_to($task->waypoints[2]) < 800) {
            $task->type = 'or';
            $task->title = 'Defined Out & Return';
        } else if ($task->waypoints->count() == 4 && $task->waypoints[0]->get_dist_to($task->waypoints[3]) < 800) {
            $task->type = 'tr';
            $task->title = 'Defined Triangle';
        } else {
            $task->type = 'go';
            $task->title = 'Goal';
        }

        $made_turnpoints = 0;
        $last_turnpoint = 0;
        foreach ($task->waypoints as $turnpoint) {
            for ($i = $last_turnpoint; $i < $this->track_points->count(); $i++) {
                if ($point->get_dist_to($turnpoint) < 800) {
                    $made_turnpoints++;
                    continue 2;
                }
            }
        }
        $this->task = $task;
        return ($this->task->waypoints->count() == $made_turnpoints);
    }

    public function start_time($formatted = false) {
        if ($formatted) {
            return date('H:i:s', $this->track_points->first()->time);
        } else {
            return $this->track_points->first()->time;
        }
    }

    public function track_open_distance_3tp($sub = false) {

        $best_results = array();

        for ($key1 = 0; $key1 < $this->calculation_subset_size; $key1++) {
            $this->calculation_subset[$key1]->bestBack = $this->maximum_bound_index($this->distance_map[$key1], 0, $key1);
            $this->calculation_subset[$key1]->bestFwrd = $this->maximum_bound_index($this->distance_map[$key1], $key1, $this->calculation_subset_size);
        }
        $this->console("Max legs calculated");
        $maximum_distance_between_two_points = 0;
        $indexes[0] = 0;
        $indexes[1] = 0;
        $indexes[2] = 0;
        $indexes[3] = 0;
        $indexes[4] = 0;
        $rcnt = -1;
        for ($row = 0; $row <= $this->calculation_subset_size - 1; ++$row) {
            $rcnt++;
            $maxF = 0;
            $midF = 0;
            $endF = 0;
            $endB = 0;
            $maxB = 0;
            $midB = 0;
            for ($key2 = 1; $key2 <= $row; ++$key2) {
                if (($this->distance_map[$row][$key2] + $this->calculation_subset[$key2]->bestBack[0]) > $maxB) {
                    $maxB = $this->distance_map[$row][$key2] + $this->calculation_subset[$key2]->bestBack[0];
                    $midB = $key2;
                    $endB = $this->calculation_subset[$key2]->bestBack[1];
                }
            }
            for ($key2 = $row; $key2 < $this->calculation_subset_size; ++$key2) {
                if (($this->distance_map[$row][$key2] + $this->calculation_subset[$key2]->bestFwrd[0]) > $maxF) {
                    $maxF = $this->distance_map[$row][$key2] + $this->calculation_subset[$key2]->bestFwrd[0];
                    $midF = $key2;
                    $endF = $this->calculation_subset[$key2]->bestFwrd[1];
                }
            }
            if ($maxF + $maxB > $maximum_distance_between_two_points) {
                $maximum_distance_between_two_points = $maxF + $maxB;
                $best_results[] = array($endB, $midB, $row, $midF, $endF);
            }
        }
        if (!$sub) {
            $dist_map_backup = $this->distance_map;
            $this->c_backup = $this->calculation_subset;
            $skip_backup = $this->maximum_distance_between_two_points;
            $cnt = 0;
            foreach (array_reverse($best_results) as $result) {
                $cnt++;
                $this->get_dist_remap($result);
                $this->track_open_distance_3tp(true);
                if ($cnt == 10) {
                    break;
                }
            }
            $this->calculation_subset = $this->c_backup;
            $this->calculation_subset_size = count($this->calculation_subset);
            $this->distance_map = $dist_map_backup;
            $this->maximum_distance_between_two_points = $skip_backup;
        } else {
            if ($maximum_distance_between_two_points > $this->od->_temp_distance) {
                $this->od->_temp_distance = $maximum_distance_between_two_points;
                $this->od->set($this->get_list(end($best_results)));
            }
        }
    }

    function track_out_and_return($sub = false) {
        $best_results = array();
        $maximum_distance_between_two_points = 0;
        $indexes[0] = 0;
        $indexes[1] = 0;
        $indexes[2] = 0;
        for ($row = 0; $row < $this->calculation_subset_size; ++$row) {
            $minLeg = 805;
            $row_plus = $row + 2;
            for ($col = $this->calculation_subset_size - 1; $col > $row_plus; --$col) {
                if ($this->distance_map[$row][$col] > $minLeg) {
                    $col -= $step = (int) (($this->distance_map[$row][$col] - $minLeg) / $this->maximum_distance_between_two_points);
                    continue;
                }
                $x = $this->furthest_between($row, $col);
                if (($this->distance_map[$row][$x] * 2 - $this->distance_map[$row][$col]) > $maximum_distance_between_two_points) {
                    $maximum_distance_between_two_points = $this->distance_map[$row][$x] * 2 - $this->distance_map[$row][$col];
                    $best_results[] = array($row, $x, $col);
                    $minLeg = $this->distance_map[$row][$col];
                }
            }
        }
        if (!$sub) {
            $dist_map_backup = $this->distance_map;
            $this->c_backup = $this->calculation_subset;
            $skip_backup = $this->maximum_distance_between_two_points;
            $cnt = 0;
            foreach (array_reverse($best_results) as $result) {
                $cnt++;
                $this->get_dist_remap($result);
                $this->track_out_and_return(true);
                if ($cnt == 10) {
                    break;
                }
            }
            $this->calculation_subset = $this->c_backup;
            $this->calculation_subset_size = count($this->calculation_subset);
            $this->distance_map = $dist_map_backup;
            $this->maximum_distance_between_two_points = $skip_backup;
        } else {
            if ($maximum_distance_between_two_points > $this->or->_temp_distance) {
                $this->or->_temp_distance = $maximum_distance_between_two_points;
                $this->or->set($this->get_list(end($best_results)));
            }
        }
    }

    function track_flat_triangles($sub = false, $min_ratio = 0) {
        return $this->track_triangles($sub, $min_ratio, $type = 'ft');
    }

    function track_triangles($sub = false, $min_ratio = 0.28, $type = 'tr') {
        $best_results = array();
        $closest_end = 0;

        $maximum_distance_between_two_points = 0;
        $minleg = max(800, $this->or->_temp_distance * 2 / 9);
        // for each entry in the dist table (moving forward)
        for ($row = 0; $row < $this->calculation_subset_size; ++$row) {
            for ($col = $this->calculation_subset_size - 1; $col > $row && $col > $closest_end; --$col) {
                // if the distance between the points is < 800m
                if ($this->distance_map[$row][$col] > 800) {
                    $col -= (int) (($this->distance_map[$row][$col] - 800) / $this->maximum_distance_between_two_points);
                    continue;
                }
                for ($x = $row; $x <= $col - 2; ++$x) {
                    $x_plus = $x + 1;
                    for ($z = $col; $z > $x_plus; --$z) {
                        if ($this->distance_map[$x][$z] < $minleg) {
                            $z -= (int) (($minleg - $this->distance_map[$x][$z]) / $this->maximum_distance_between_two_points);
                            continue;
                        }
                        $z_minus = $z - 1;
                        for ($y = (int) floor(($x + $z) / 2); $y <= $z_minus; ++$y) {
                            if ($this->distance_map[$x][$y] < $minleg) {
                                $y += (int) (($minleg - $this->distance_map[$x][$y]) / $this->maximum_distance_between_two_points);
                                continue;
                            }
                            if ($this->distance_map[$y][$z] < $minleg) {
                                $y += (int) (($minleg - $this->distance_map[$y][$z]) / $this->maximum_distance_between_two_points);
                                continue;
                            }
                            $d = ($this->distance_map[$x][$y] + $this->distance_map[$y][$z] + $this->distance_map[$z][$x]);
                            $min = min($this->distance_map[$x][$y], $this->distance_map[$y][$z], $this->distance_map[$z][$x]);
                            if ($d > $maximum_distance_between_two_points && $min > ($min_ratio * $d)) {
                                $maximum_distance_between_two_points = $d;
                                $best_results[] = array($row, $x, $y, $z, $col);
                                $minleg = max(1200, $d * $min_ratio);
                            }
                        }
                        for ($y = (int) floor(($x + $z) / 2); $y >= $x_plus; --$y) {
                            if ($this->distance_map[$x][$y] < $minleg) {
                                $y -= (int) (($minleg - $this->distance_map[$x][$y]) / $this->maximum_distance_between_two_points);
                                continue;
                            }
                            if ($this->distance_map[$y][$z] < $minleg) {
                                $y -= (int) (($minleg - $this->distance_map[$y][$z]) / $this->maximum_distance_between_two_points);
                                continue;
                            }
                            $d = ($this->distance_map[$x][$y] + $this->distance_map[$y][$z] + $this->distance_map[$z][$x]);
                            $min = min($this->distance_map[$x][$y], $this->distance_map[$y][$z], $this->distance_map[$z][$x]);
                            if ($d > $maximum_distance_between_two_points && $min > ($min_ratio * $d)) {
                                $maximum_distance_between_two_points = $d;
                                $best_results[] = array($row, $x, $y, $z, $col);
                                $minleg = max(1200, $d * $min_ratio);
                            }
                        }
                    }
                }
                $closest_end = $col;
                $col = 0;
                //$row=$this->nChoosen;
            }
        }
        if (!$sub) {
            $dist_map_backup = $this->distance_map;
            $this->c_backup = $this->calculation_subset;
            $skip_backup = $this->maximum_distance_between_two_points;
            $cnt = 0;
            foreach (array_reverse($best_results) as $result) {
                $cnt++;
                $this->get_dist_remap($result);
                $this->track_triangles(true, $min_ratio, $type);
                if ($cnt == 10) {
                    break;
                }
            }
            $this->maximum_distance_between_two_points = $skip_backup;
            $this->calculation_subset = $this->c_backup;
            $this->calculation_subset_size = count($this->calculation_subset);
            $this->distance_map = $dist_map_backup;
        } else {
            if ($maximum_distance_between_two_points > $this->$type->_temp_distance) {
                $this->$type->_temp_distance = $maximum_distance_between_two_points;
                $res = end($best_results);
                $gap = $this->distance_map[$res[0]][$res[4]];
                for ($a = $res[4]; $a >= $res[3]; $a--) {
                    for ($b = $res[0]; $b < $res[1]; $b++) {
                        if ($this->distance_map[$b][$a] < $gap) {
                            $res[4] = $a;
                            $res[0] = $b;
                            $gap = $this->distance_map[$b][$a];
                        } else {
                            $b += (int) (($this->distance_map[$b][$a] - $gap) / $this->maximum_distance_between_two_points);
                        }
                    }
                }
                unset($best_results[count($best_results) - 1]);
                while (count($best_results)) {
                    $sres = end($best_results);
                    $sgap = $this->distance_map[$sres[0]][$sres[4]];
                    if (($d = ($this->distance_map[$sres[1]][$sres[2]] + $this->distance_map[$sres[2]][$sres[3]] + $this->distance_map[$sres[2]][$sres[3]])) > $maximum_distance_between_two_points - $gap) {
                        for ($a = $sres[4]; $a >= $sres[3]; $a--) {
                            for ($b = $sres[0]; $b < $sres[1]; $b++) {
                                if ($this->distance_map[$b][$a] < $sgap) {
                                    $sres[4] = $a;
                                    $sres[0] = $b;
                                    $sgap = $this->distance_map[$b][$a];
                                } else {
                                    $b += (int) (($this->distance_map[$b][$a] - $gap) / $this->maximum_distance_between_two_points);
                                }
                            }
                        }
                        if ($d - $sgap > $maximum_distance_between_two_points - $gap) {
                            $maximum_distance_between_two_points = $d;
                            $gap = $sgap;
                            $res = $sres;
                        }
                        unset($best_results[count($best_results) - 1]);
                    } else {
                        break;
                    }
                }
                $this->$type->set($this->get_list($res));
            }
        }
    }

    public function trim() {
        if ($this->track_parts->count() > 1) {
            while (1) {
                if ($this->track_parts->first()->size() < 20) {
                    $this->console("Beginning section ignored, less than 20 points", $this, 1, 1);
                    $this->track_points->remove_first($this->track_parts->first()->size());
                    $this->track_parts->reduce_index($this->track_parts->first()->size());
                    $this->track_parts->remove_first();
                    continue;
                }
                $midPoint = round(($this->track_parts->first()->size()) / 2);
                $coord1 = $this->track_points[$this->track_parts->first()->start_point];
                $coord2 = $this->track_points[$this->track_parts->first()->start_point + $midPoint];
                $coord3 = $this->track_points[$this->track_parts->first()->end_point];
                if ($coord1->get_dist_to($coord2) + $coord2->get_dist_to($coord3) < .200) {
                    $this->console("Beginning section ignored, less than 100m", $this, 1, 1);
                    $this->track_points->remove_first($this->track_parts->first()->size());
                    $this->track_parts->reduce_index($this->track_parts->first()->size());
                    $this->track_parts->remove_first();
                } else break;
            }
            while (1) {
                if ($this->track_parts->last()->size() < 20) {
                    $this->console("End section ignored, less than 20 points", $this, 1, 1);
                    $this->track_points->remove_last($this->track_parts->last()->size());
                    $this->track_parts->remove_last();
                    continue;
                }
                $midPoint = round(($this->track_parts->last()->size()) / 2);
                $coord1 = $this->track_points[$this->track_parts->last()->start_point];
                $coord2 = $this->track_points[$this->track_parts->last()->start_point + $midPoint];
                $coord3 = $this->track_points[$this->track_parts->last()->end_point];
                if ($coord1->get_dist_to($coord2) + $coord2->get_dist_to($coord3) < .200) {
                    $this->console("End section ignored, less than 100m", $this, 1, 1);
                    $this->track_points->remove_last($this->track_parts->last()->size());
                    $this->track_parts->remove_last();
                } else break;
            }
        } else {
            $this->console('1 Part');
        }
        foreach ($this->track_points as $key => $index) {
            $index->id = $key;
        }
    }

    public function truncate($start, $end = 0) {
        if (!$end) {
            $end = $this->track_points->count();
        }
        $points = $this->track_points->subset($start, $end);
        $this->track_points->exchangeArray($points);
    }

    private function get_meta_linestring() {
        $output = '<Placemark>';
        $output .= '<Style><LineStyle><color>ffff0000</color><width>2</width></LineStyle></Style>';
        $output .= $this->get_time_meta_data();
        $output .= $this->get_kml_linestring();
        $output .= '</Placemark>';
        return $output;
    }
}

class track_point {
    public $alt = 0;
    public $bestBack;
    public $bestFwrd;
    public $climbRate = 0;
    public $cos_lat = 0;
    public $ele = 0;
    public $id = 0;
    public $lat = 0;
    public $lon = 0;
    public $lonRad = 0;
    public $latRad = 0;
    public $sin_lat = 0;
    public $speed = 0;
    public $time = 0;
    public $val = 0;

    public function get_coordinate() {
        return geometry::lat_long_to_os(($this->lat), ($this->lon));
    }

    public function get_dist_to(track_point $b) {
        return geometry::get_distance($this, $b);
    }

    public function get_dist_to_precise(track_point $b) {
        return geometry::get_distance_ellipsoid($this, $b);
    }

    public function get_js_coordinate($time = 0) {
        $coord = array($this->lat, $this->lon, $this->ele, $time, $this->climbRate, $this->speed, $this->bearing);
        return $coord;
    }

    public function get_kml_coordinate($time = null) {
        if ($time !== null)
            return sprintf("%8f,%8f,%-5d,%6d ", $this->lon, $this->lat, $this->ele, $time);
        else
            return sprintf("%8f,%8f,%-5d ", $this->lon, $this->lat, $this->ele);
    }

    public function get_kml_point() {
        $xml = '<Point><altitudeMode>absolute</altitudeMode><coordinates>' . $this->get_kml_coordinate() . '</coordinates></Point>';
        return $xml;
    }

    public function get_time_to(track_point $b) {
        return $b->time - $this->time;
    }
}

class track_part {
    public $end;
    public $end_point;
    public $start;
    public $start_point;
    public $time;

    public function __construct(track_point $point, $pos) {
        $this->start = $point;
        $this->start_point = $pos;
    }

    public function finish(track_point $point, $pos) {
        $this->end_point = $pos;
        $this->end = $point;
    }

    public function get_time() {
        if (!isset($this->time)) {
            $this->start->get_time_to($this->end);
        }
        return $this->time;
    }

    public function reduce_index($int) {
        $this->start_point -= $int;
        $this->end_point -= $int;
    }

    public function size() {
        return $this->end_point - $this->start_point + 1;
    }
}

class task {
    public $_temp_distance = 0;
    public $coordinates;
    public $distance;
    public $timestamp;
    public $title;
    /** @var track_point_array */
    public $waypoints;

    public function __construct($title = '') {
        $this->title = $title;
    }

    public function get_coordinates() {
        if (!isset($this->coordinates)) {
            if (isset($this->waypoints)) {
                $this->coordinates = $this->waypoints->get_coordinates(range(0, $this->waypoints->count() - 1));
            } else {
                return '';
            }
        }
        return $this->coordinates;
    }

    public function get_distance($dp = 10) {
        if (!isset($this->distance)) {
            if (isset($this->waypoints)) {
                $this->distance = $this->waypoints->get_distance();
            } else {
                $this->distance = 0;
            }
        }
        return number_format($this->distance, $dp);
    }

    public function get_duration() {
        if (!isset($this->timestamp)) {
            $this->timestamp = $this->waypoints->last()->time - $this->waypoints->first()->time;
        }
        return $this->timestamp;
    }

    public function get_formatted_time() {
        return date('H:i:s', $this->timestamp);
    }

    protected function get_kml_table() {
        $html = '';
        $tot = 0;
        $last = null;
        foreach ($this->waypoints as $key => $point) {
            $distance = number_format(($last ? $point->get_dist_to($last) : 0), 2);
            $tot += $distance;
            $html .= '
                <tr>
                    <td>' . $key . '</td>
                    <td>' . $point->lat . '</td>
                    <td>' . $point->lon . '</td>
                    <td>' . $point->get_coordinate() . '</td>
                    <td>' . $distance . '</td>
                    <td>' . $tot . '</td>
                </tr>';
            $last = $point;
        }
        return $html;
    }

    public function get_kml_coordinates() {
        return $this->waypoints->get_kml_coordinates();
    }

    public function get_kml_track($colour, $title = '') {
        $output = '';
        if (isset($this->waypoints)) {
            $table_inner = $this->get_kml_table();
            $coordinates = $this->get_kml_coordinates();
            $output = '
<Placemark>
    <visibility>1</visibility>
    <name>' . $title . '</name>
    <description>
        <![CDATA[
        <pre>
            <table>
                <thead>
                    <th style="padding:0 4px">TP</th>
                    <th style="padding:0 4px">Latitude</th>
                    <th style="padding:0 4px">Longitude</th>
                    <th style="padding:0 4px">OS Gridref</th>
                    <th style="padding:0 4px">Distance</th>
                    <th style="padding:0 4px">Total</th>
                </thead>
                <tbody>
                    ' . $table_inner . '
                    <tr><td colspan = "4" style="text-align:right">Duration</td><td colspan="2"  style="text-align:right">' . $this->get_formatted_time() . '</td></tr>
                </tbody>
            </table>
        </pre>
        ]]>
    </description>
    <Style>
        <LineStyle>
            <color>FF' . $colour . '</color>
            <width>2</width>
        </LineStyle>
    </Style>
    <LineString>
        <coordinates>
            ' . implode(' ', $coordinates) . '
        </coordinates>
    </LineString>
</Placemark>';
        }

        return $output;
    }

    public function get_session_coordinates() {
        if (!isset($this->coordinates)) {
            if (isset($this->waypoints)) {
                $this->coordinates = $this->waypoints->get_session_coordinates(range(0, $this->waypoints->count() - 1));
            } else {
                return '';
            }
        }
        return $this->coordinates;
    }

    public function get_time() {
        return $this->timestamp;
    }

    public function get_waypoints_from_os() {
        $this->waypoints = new track_point_array();
        $coords = explode(';', $this->coordinates);
        foreach ($coords as $coord) {
            list($coord, $ele) = explode(':', $coord);
            $latlng = geometry::os_to_lat_long($coord);
            $track_point = new track_point();
            $track_point->lat = $latlng[0];
            $track_point->lon = $latlng[1];
            $track_point->ele = $ele;
            $this->waypoints[] = $track_point;
        }
    }

    public function set($indexes) {
        $this->distance = null;
        $this->waypoints = new track_point_array();
        foreach ($indexes as $track_point) {
            $this->waypoints[] = $track_point;
        }
        $this->timestamp = $this->waypoints->last()->time - $this->waypoints->first()->time;
    }


}

class out_and_return extends task {
    public function get_distance($dp = 10) {
        if (!isset($this->distance)) {
            if (isset($this->waypoints)) {
                if ($this->waypoints->spherical) {
                    $this->distance = $this->waypoints[0]->get_dist_to($this->waypoints[1]) * 2 - $this->waypoints[0]->get_dist_to($this->waypoints[2]);
                } else {
                    $this->distance = $this->waypoints[0]->get_dist_to_precise($this->waypoints[1]) * 2 - $this->waypoints[0]->get_dist_to_precise($this->waypoints[2]);
                }
            } else {
                $this->distance = 0;
            }
        }
        return number_format($this->distance, $dp);
    }
}

class triangle extends task {
    public function get_distance($dp = 10) {
        if (!isset($this->distance)) {
            if (isset($this->waypoints) && $this->waypoints->count() == 5) {
                if ($this->waypoints->spherical) {
                    $this->distance = $this->waypoints[1]->get_dist_to($this->waypoints[2]) + $this->waypoints[2]->get_dist_to($this->waypoints[3]) + $this->waypoints[1]->get_dist_to($this->waypoints[3]) - $this->waypoints[0]->get_dist_to($this->waypoints[4]);
                } else {
                    $this->distance = $this->waypoints[1]->get_dist_to_precise($this->waypoints[2]) + $this->waypoints[2]->get_dist_to_precise($this->waypoints[3]) + $this->waypoints[1]->get_dist_to_precise($this->waypoints[3]) - $this->waypoints[0]->get_dist_to_precise($this->waypoints[4]);
                }
            } else {
                $this->distance = 0;
            }
        }
        return number_format($this->distance, $dp);
    }

    protected function get_kml_table() {
        $html = '';
        if ($this->waypoints->count() == 5) {
            $waypoints = $this->waypoints;
            $this->waypoints = new track_point_array(array($waypoints[1], $waypoints[2], $waypoints[3], $waypoints[1]));
            $html = parent::get_kml_table();
            $this->waypoints = $waypoints;
        }
        return $html;
    }

    public function get_kml_coordinates() {
        $html = '';
        if ($this->waypoints->count() == 5) {
            $waypoints = $this->waypoints;
            $this->waypoints = new track_point_array(array($waypoints[1], $waypoints[2], $waypoints[3], $waypoints[1]));
            $html = parent::get_kml_coordinates();
            $this->waypoints = $waypoints;
        }
        return $html;
    }
}

class track_part_array extends object_array {

    /**
     * @return track_part
     */
    public function first() {
        return parent::first();
    }

    /**
     * @return track_part
     */
    public function last() {
        return parent::last();
    }

    public function reduce_index($int) {
        for ($i = 0; $i < $this->count(); $i++)
            $this->offsetGet($i)->reduce_index($int);
    }
}

class track_point_array extends object_array {

    /**
     * @return track_point
     */
    public $spherical = true;

    public function first() {
        return parent::first();
    }

    /**  @return string */
    public function get_coordinates(array $indexes) {
        $coordinates = array();
        foreach ($indexes as $index) {
            $coordinates[] = $this[$index]->get_coordinate();
        }
        return implode(';', $coordinates);
    }

    public function get_distance() {
        $distance = 0;
        if ($this->count() && $this->count() > 2) {
            foreach (range(0, $this->count() - 2) as $index) {
                if ($this->spherical) {
                    $distance += $this[$index]->get_dist_to($this[$index + 1]);
                } else {
                    $distance += $this[$index]->get_dist_to_precise($this[$index + 1]);
                }
            }
        }
        return $distance;
    }

    public function get_kml_coordinates() {
        $coordinates = array();
        if ($this->count()) {
            foreach (range(0, $this->count() - 1) as $index) {
                $coordinates[] = $this[$index]->get_kml_coordinate();
            }
        }
        return $coordinates;
    }

    public function get_session_coordinates($indexes) {
        $coordinates = array();
        foreach ($indexes as $index) {
            $coordinates[] = $this[$index]->get_coordinate() . ':' . $this->ele;
        }
        return implode(';', $coordinates);
    }

    public function remove_first($int = 1) {
        parent::remove_first($int);
    }

    /**
     * @return track_point
     */
    public function last() {
        return parent::last();
    }
}

class coordinate_bound {
    public $east = -360;
    public $north = -180;
    public $south = 180;
    public $west = 360;

    public function add_bounds_to_bound(coordinate_bound $bound) {
        $this->north = max($bound->north, $this->north);
        $this->east = max($bound->east, $this->east);
        $this->south = min($bound->south, $this->south);
        $this->west = min($bound->west, $this->west);
    }

    public function add_coordinate_to_bounds($lat, $lon) {
        if ($lon < $this->west) {
            $this->west = $lon;
        }
        if ($lon > $this->east) {
            $this->east = $lon;
        }
        if ($lat > $this->north) {
            $this->north = $lat;
        }
        if ($lat < $this->south) {
            $this->south = $lat;
        }
    }

    public function crosses_antimeridian() {
        return $this->west > $this->east;
    }

    public function get_center() {
        $class = new stdClass();
        $class->lat = ($this->south + $this->north) / 2;
        $class->lon = $this->crosses_antimeridian() ? $this->normalize_lon($this->west + $this->get_lon_center($this->west, $this->east) / 2) : ($this->west + $this->east) / 2;
        return $class;
    }

    public function get_js() {
        $class = new stdClass();
        $class->north = $this->north;
        $class->east = $this->east;
        $class->south = $this->south;
        $class->west = $this->west;
        $class->center = $this->get_center();
        $class->range = $this->get_range();
        return $class;
    }

    public function get_kml_viewport() {

    }

    public function get_lon_center($west, $east) {
        return ($west > $east) ? ($east + 360 - $west) : ($east - $west);
    }

    public function get_range() {
        $ne = new track_point();
        $ne->lat = $this->north;
        $ne->lon = $this->east;
        $sw = new track_point();
        $sw->lat = $this->south;
        $sw->lon = $this->west;
        $dist = $sw->get_dist_to($ne);
        return $dist * 5;
    }

    public function normalize_lon($lon) {
        if ($lon % 360 == 180) {
            return 180;
        }
        $l = $lon % 360;
        return $l < -180 ? $l + 360 : $l > 180 ? $l - 360 : $l;
    }
}

class object_array extends ArrayObject {

    private $first_index = 0;

    public function first() {
        return $this[0];
    }

    public function first_index() {
        return $this->first_index;
    }

    public function last() {
        return $this[$this->count() - 1];
    }

    public function remove_first($int = 1) {
        parent::__construct($this->subset($int));
    }

    public function remove_last($int = 0) {
        if ($int) {
            for ($i = 0; $i < $int; $i++)
                $this->remove_last();
        } else {
            $this->offsetUnset($this->count() - 1);
        }
    }

    public function subset($start = 0, $end = null) {
        $sub = array();
        if ($end == null || $end < $start)
            $end = $this->count();
        for ($i = $start; $i < $end; $i++) {
            $sub[] = $this[$i];
        }
        return $sub;
    }
}

class stat_object {
    public $min = null;
    /** @var track_point */
    public $min_point;
    public $max = null;
    /** @var  track_point */
    public $max_point;
    public $average = null;
    public $total = null;
}
