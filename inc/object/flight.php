<?php

namespace object;

use classes\ajax;
use classes\geometry;
use classes\get;
use classes\jquery;
use core\classes\db;
use core\classes\table;
use html\node;
use track\track;
use traits\table_trait;

/**
 * @property mixed club_title
 * @property mixed invis_info
 * @property mixed winter
 * @property mixed delayed
 */
class
flight extends table {

    use table_trait;

    public $added;
    public $admin_info;
    public $base_score;
    public $cid;
    public $club_name;
    public $coords;
    public $defined;
    public $did;
    /** @var int Flight ID */
    public $fid;
    /** @var string Date flown */
    public $date;
    public $ftid;
    public $gid;
    public $glider_name;
    public $lid;
    public $manufacturer_title;
    public $multi;
    /** @var string Pilot name */
    public $p_name;
    /** @var string Club name */
    public $c_name;
    /** @var string Glider name */
    public $g_name;
    /** @var string Glider manufacture name */
    public $gm_title;
    /** @var string Coordinates for the flight */
    public $coordinates;
    /** @var int glider class - 1 or 5 for rigid or flex */
    public $class;
    /** @var int The id used for a flight, depends on whether $class is 1 or 5 */
    public $ClassID;
    public $pid;
    public $speed;

    /** @var track */
    public $track = null;

    public static $launch_types = array(0 => 'Foot', 1 => 'Aerotow', 2 => 'Winch');
    public static $module_id = 2;
    public $pilot_name;
    public $ridge;
    public $score;
    public $table_key = 'fid';
    public $time;
    public $vis_info;
    public $season;
    public $duration;
    public $od_score;
    public $od_time;
    public $od_coordinates;
    public $or_score;
    public $or_time;
    public $or_coordinates;
    public $tr_score;
    public $tr_time;
    public $tr_coordinates;
    public $ft_score;
    public $ft_time;
    public $ft_coordinates;

    public static $default_joins = array(
        'pilot' => 'flight.pid = pilot.pid',
        'glider' => 'flight.gid = glider.gid',
        'club' => 'flight.cid = club.cid',
        'manufacturer' => 'glider.mid = manufacturer.mid'
    );
    public static $default_fields = array(
        'flight.*',
        'pilot.name',
        'club.title',
        'glider.name',
        'manufacturer.title'
    );


    /**
     *
     */
    public function download() {
        $id = (int) $_REQUEST['id'];
        $this->do_retrieve(
            array('flight.*', 'pilot.name'),
            array(
                'join' => array('pilot' => 'flight.pid=pilot.pid'),
                'where_equals' => array('flight.fid' => $id)
            )
        );
        header("Content-type: application/octet-stream");
        header("Cache-control: private");
        $fullPath = '';
        if ((isset($this->fid) && $this->fid) || isset($_REQUEST['temp'])) {
            if (!isset($_REQUEST['type']) || $_REQUEST['type'] == 'kml') {
                $fullPath = root . '/uploads/flight/' . $id . '/track_earth.kml';
            } else if ($_REQUEST['type'] == 'igc') {
                $fullPath = root . '/uploads/flight/' . $id . '/track.igc';
            } else if ($_REQUEST['type'] == 'kmz') {
                $zip = zip_open(root . '/uploads/flight/' . (isset($_REQUEST['temp']) ? 'temp/' : '') . $id . '/track.kmz');
                $fullPath = zip_read($zip);
                $size = zip_entry_filesize($fullPath);
                $file = zip_entry_read($fullPath, $size);
                header("Content-length: $size");
                header('Content-Disposition: filename="' . $id . (!isset($_REQUEST['temp']) ? '-' . str_replace(' ', '_', $this->pilot_name) . '-' . $this->date : '') . '.kml"');
                echo $file;
                zip_close($zip);
                return;
            }
            if ($fullPath && $fd = fopen($fullPath, "r")) {
                $fsize = filesize($fullPath);
                header('Content-Disposition: filename="' . $id . '-' . str_replace(' ', '_', $this->pilot_name) . '-' . $this->date . '.' . $_REQUEST['type'] . '"');
                header("Content-length: $fsize");
                while (!feof($fd)) {
                    $buffer = fread($fd, 2048);
                    echo $buffer;
                }
                fclose($fd);
            }
        }
    }

    /**
     * @return mixed
     */
    public function get_best_score() {
        $scores = array(
            array($this->od_score * $this->get_multiplier(flight_type::OD_ID, $this->season), flight_type::OD_ID),
            array($this->or_score * $this->get_multiplier(flight_type::OR_ID, $this->season), flight_type::OR_ID),
            array($this->tr_score * $this->get_multiplier(flight_type::TR_ID, $this->season), flight_type::TR_ID),
            array($this->ft_score * $this->get_multiplier(flight_type::FT_ID, $this->season), flight_type::FT_ID),
        );
        usort($scores, function ($a, $b) {
                return $a[0] - $b[0];
            }
        );
        return end($scores);
    }

    /**
     * @param null $type
     * @param null $season
     * @return int|mixed
     */
    public function get_multiplier($type = null, $season = null) {
        if (!$this->ridge) {
            return flight_type::get_multiplier(isset($type) ? $type : $this->ftid, isset($season) ? $season : $this->season, $this->ridge);
        } else {
            return 1;
        }
    }

    /**
     *
     */
    public static function get_statistics() {
        $year_stats = [];
        foreach (range(1991, 2013) as $year) {
            $year_object = new \stdClass();
            $year_object->coords = [];
            $year_object->minEle = $year_object->min_cr = $year_object->maximum_cr = $year_object->maxEle = 0;
            $year_object->min_cr = 0;
            $year_object->drawGraph = true;
            $year_object->colour = get::js_colour($year);
            foreach (range(1, 12) as $month) {
                $score = db::select('flight')
                    ->retrieve('sum(score) AS score')
                    ->filter(['YEAR(date)=:year', 'MONTH(date)=:month'], ['year' => $year, 'month' => $month])
                    ->execute()
                    ->fetchObject()
                    ->score;
                $tot = db::count('flight', 'fid')
                    ->filter(['YEAR(date)=:year', 'MONTH(date)=:month'], ['year' => $year, 'month' => $month])
                    ->execute();
                $year_object->coords[] = [0, 0, $tot, $month, $score];
                $year_object->maxEle = max($year_object->maxEle, $tot);
                $year_object->maximum_cr = max($year_object->maximum_cr, $score);
            }
            $year_stats[] = $year_object;
        }
        $wrapper = new \stdClass();
        $inner = new \stdClass();
        $inner->track = $year_stats;
        $inner->StartT = 1;
        $inner->EndT = 12;
        $wrapper->nxcl_data = $inner;
        return json_encode($wrapper);
    }

    public function get_flights_by_area() {
        $flights = flight::get_all(array(), array('where' => 'did > 1 AND date < "08-28-2012" AND (os_codes LIKE "%SE%" OR os_codes LIKE "%SK%" OR os_codes LIKE "%TA%" OR os_codes LIKE "%TF%")', 'order' => 'fid DESC'));
        $flights->iterate(function (flight $flight, $cnt) {
                $path = $flight->get_igc();
                copy($path, root . '/temp/pre/' . $cnt . '.kml');
            }
        );

        $flights = flight::get_all(array(), array('where' => 'did > 1 AND date >= "08-28-2012" AND (os_codes LIKE "%SE%" OR os_codes LIKE "%SK%" OR os_codes LIKE "%TA%" OR os_codes LIKE "%TF%")', 'order' => 'fid DESC'));
        $flights->iterate(function (flight $flight, $cnt) {
                $path = $flight->get_igc();
                copy($path, root . '/temp/post/' . $cnt . '.kml');
            }
        );
    }

    /**
     *
     */
    public function generate_benchmark() {
        $flights = flight::get_all(array(), array('where' => 'did > 1', 'order' => 'fid DESC'));
        $total_time = 0;
        $flights->iterate(
            function (flight $flight) use (&$total_time) {
                $track = new track();
                $track->time = 0;
                $track->id = $flight->fid;
                $time = time();
                echo node::create('p', [], 'Track :' . $flight->fid);
                if($track->parse_IGC()) {
                    $grid_refs = [];
                    /** @var \classes\lat_lng $point */
                    foreach($track->track_points as $point) {
                        $grid_refs = array_merge($grid_refs, [$point->get_grid_cell()->code]);
                    }
                    $flight->os_codes = implode(',', array_unique($grid_refs));
                    $flight->do_save();
                /*if ($track->generate($flight)) {
                    $time = time() - $time;
                    $total_time += $time;
                    $flight->time = $time;
                    $best_score = $flight->get_best_score();
                    switch ($flight->ftid) {
                        case  1:
                            if ($track->od->get_distance() > $flight->base_score) {
                                echo node::create('span', ['style' => 'color:#00ff00'], 'OD Gained ' . ($track->od->get_distance() - $flight->base_score) . 'km (' . ($track->od->get_distance() / ($track->od->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
                            } else {
                                echo node::create('span', ['style' => 'color:#ff0000'], 'OD Lost ' . ($flight->base_score - $track->od->get_distance()) . 'km (' . ($track->od->get_distance() / ($track->od->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
                            }
                            break;
                        case
                        2:
                            if ($track->or->get_distance() > $flight->base_score) {
                                echo node::create('span', ['style' => 'color:#00ff00'], 'OR Gained ' . ($track->or->get_distance() - $flight->base_score) . 'km (' . ($track->or->get_distance() / ($track->or->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
                            } else {
                                echo node::create('span', ['style' => 'color:#ff0000'], 'OR Lost ' . ($flight->base_score - $track->or->get_distance()) . 'km (' . ($track->or->get_distance() / ($track->or->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
                            }
                            break;
                        case  3:
                            break;
                        case  4:
                            if ($track->tr->get_distance() > $flight->base_score) {
                                echo node::create('span', ['style' => 'color:#00ff00'], 'TR Gained ' . ($track->tr->get_distance() - $flight->base_score) . 'km (' . ($track->tr->get_distance() / ($track->tr->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
                            } else {
                                echo node::create('span', ['style' => 'color:#ff0000'], 'TR Lost ' . ($flight->base_score - $track->tr->get_distance()) . 'km (' . ($track->tr->get_distance() / ($track->tr->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
                            }
                            break;
                        case  5:
                            if ($track->ft->get_distance() > $flight->base_score) {
                                echo node::create('span', ['style' => 'color:#00ff00'], 'TR Gained ' . ($track->ft->get_distance() - $flight->base_score) . 'km (' . ($track->ft->get_distance() / ($track->ft->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
                            } else {
                                echo node::create('span', ['style' => 'color:#ff0000'], 'TR Lost ' . ($flight->base_score - $track->ft->get_distance()) . 'km (' . ($track->ft->get_distance() / ($track->ft->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
                            }
                            break;
                    }
                    if ($flight->ftid != $best_score[1]) {
                        echo 'Flight Scored better as a' . $best_score[1];
                        switch ($best_score[0]) {
                            case  flight_type::OD_ID:
                                $flight->coords = $track->od->get_coordinates();
                                $flight->base_score = $track->od->get_distance();
                                break;
                            case  flight_type::OR_ID:
                                $flight->coords = $track->or->get_coordinates();
                                $flight->base_score = $track->or->get_distance();
                                break;
                            case  flight_type::TR_ID:
                                $flight->coords = $track->tr->get_coordinates();
                                $flight->base_score = $track->tr->get_distance();
                                break;
                            case  flight_type::FT_ID:
                                $flight->coords = $track->ft->get_coordinates();
                                $flight->base_score = $track->ft->get_distance();
                                break;
                        }
                        $flight->score = $best_score[0];
                        $flight->ftid = $best_score[1];
                        $flight->multi = $flight->get_multiplier();
                    }
                    $flight->do_save();*/
                } else {
                    $flight->time = 0;
                    echo node::create('p', [], 'Track :' . $flight->fid . ' failed to calculate');
                }
                flush();
            }
        );

        $flights->uasort(function ($a, $b) {
                return $b->time - $a->time;
            }
        );

        foreach ($flights as $flight) {
            echo node::create('p', [], 'Track :' . $flight->fid . ' scored in ' . date('H:i:s', $flight->time));
        }

        $average_time = $total_time / count($flights);

        echo node::create('p', [], 'Average Time :' . date('H:i:s', $average_time));
    }

    /**
     *
     */
    public function generate_files() {
        if (isset($_REQUEST['id'])) {
            $this->do_retrieve_from_id(array(), $_REQUEST['id']);
            if ($this->fid) {
                $track = new track();
                $track->generate($this);
                $this->do_save();
                jquery::colorbox(array('html' => 'Flight ' . $this->fid . ' generated successfully. ' . node::create('p pre', [], print_r($track->log_file, 1))));
            }
        }
    }

    /**
     *
     */
    public function get_info_ajax() {
        $html = '';
        $id = (int) $_REQUEST['fid'];
        $this->do_retrieve(
            self::$default_fields,
            array(
                'join' => array_merge(
                    self::$default_joins,
                    array('manufacturer' => 'glider.mid=manufacturer.mid')
                ),
                'where_equals' => array('flight.fid' => $id)
            )
        );
        if (!isset($this->fid) || !$this->fid) {
            $html .= 'Flight not found, this is a bug...';
        } else {
            $html = node::create('table', ['width' => '100%'],
                node::create('tr', [], node::create('td', [], 'Flight ID') . node::create('td', [], $this->fid)) .
                node::create('tr', [], node::create('td', [], 'Pilot') . node::create('td', [], $this->pilot_name)) .
                node::create('tr', [], node::create('td', [], 'Date') . node::create('td', [], date('d/m/Y', $this->date))) .
                node::create('tr', [], node::create('td', [], 'Glider') . node::create('td', [], $this->manufacturer_title . ' - ' . $this->glider_name)) .
                node::create('tr', [], node::create('td', [], 'Club') . node::create('td', [], $this->club_title)) .
                node::create('tr', [], node::create('td', [], 'Defined') . node::create('td', [], get::bool($this->defined))) .
                node::create('tr', [], node::create('td', [], 'Launch') . node::create('td', [], get::launch($this->lid))) .
                node::create('tr', [], node::create('td', [], 'Type') . node::create('td', [], get::flight_type($this->ftid))) .
                node::create('tr', [], node::create('td', [], 'Ridge Lift') . node::create('td', [], get::bool($this->ridge))) .
                node::create('tr', [], node::create('td', [], 'Score') . node::create('td', [], $this->base_score . 'x' . $this->multi . ' = ' . $this->score)) .
                node::create('tr', [], node::create('td', [], 'Coordinates') . node::create('td', [], str_replace(';', '; ', $this->coords))) .
                node::create('tr', [], node::create('td', [], 'Info') . node::create('td', [], $this->vis_info)) .

                (file_exists(root . '/uploads/flight/' . $this->fid . '/track.kmz') ?
                    node::create('tr td.center.view', ['colspan' => 2], node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flight(' . $this->fid . ')'], 'Add trace to Map')) .
                    node::create('tr td.center.view', ['colspan' => 2],
                        node::create('a.download.igc', ['href' => $this->get_download_url('igc'), 'title' => "Download IGC", 'rel' => 'external'], 'Download IGC') .
                        node::create('a.download.kml', ['href' => $this->get_download_url('kmz'), 'title' => 'Download KML', 'rel' => 'external'], 'Download KML')
                    ) :
                    node::create('tr td.center.view.coords', ['colspan' => 2], node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flightC(\'' . $this->coords . '\',' . $this->fid . ');return false;'], 'Add coordinates to map'))
                ) .
                node::create('a.close', ['title' => 'close', 'onclick' => '$("#pop").remove()'], 'Close')
            );
        }

        ajax::inject('#' . $_REQUEST['origin'], 'after', '<script>$("#pop").remove();</script>');
        ajax::inject('#' . $_REQUEST['origin'], 'after',
            node::create('div#pop', [],
                node::create('span.arrow', [], 'Arrow') .
                node::create('div.content', [], $html) .
                node::create('script', [], 'if($("#pop").offset().left > 400)$("#pop").addClass("reverse");'
                )
            )
        );
    }

    protected function  get_download_url($type = 'igc') {
        return '/?module=\object\flight&act=download&id=' . $this->fid . '&type=' . $type;
    }

    /**
     *
     */
    private function set_track() {
        $track = new track();
        $track->id = $this->fid;
        $track->parse_IGC();
        $track->trim();
        $this->track = $track;
    }

    /**
     * @return string
     */
    public function get_stats() {
        if (!isset($this->track)) {
            $this->set_track();
        }
        $this->track->get_graph_values();
        $height = $this->track->get_stats('ele');
        $speed = $this->track->get_stats('speed');
        $climb = $this->track->get_stats('climbRate');
        $html = node::create('table.stats', [],
            node::create('thead tr', [],
                node::create('th', [], '') .
                node::create('th', [], 'Min') .
                node::create('th', [], 'Max') .
                node::create('th', [], 'Average')
            ) .
            node::create('tbody', [],
                node::create('tr', [],
                    node::create('td', [], 'Elevation') .
                    node::create('td', [], $height->min . 'ft <span>@ ' . date('H:i:s', $height->min_point->time)) .
                    node::create('td', [], $height->max . 'ft <span>@ ' . date('H:i:s', $height->max_point->time)) .
                    node::create('td', [], number_format($height->average, 2) . 'm')
                ) .
                node::create('tr', [],
                    node::create('td', [], 'Speed') .
                    node::create('td', [], $speed->min . 'km/h <span>@ ' . date('H:i:s', $speed->min_point->time)) .
                    node::create('td', [], $speed->max . 'km/h <span>@ ' . date('H:i:s', $speed->max_point->time)) .
                    node::create('td', [], number_format($speed->average, 2) . 'km/h')
                ) .
                node::create('tr', [],
                    node::create('td', [], 'Speed') .
                    node::create('td', [], $climb->min . 'ft/s <span>@ ' . date('H:i:s', $climb->min_point->time)) .
                    node::create('td', [], $climb->max . 'ft/s <span>@ ' . date('H:i:s', $climb->max_point->time)) .
                    node::create('td', [], number_format($climb->average, 2) . 'ft/s')
                )
            )
        );
        return $html;
    }

    /**
     * @return string
     */
    private function coord_info() {
        $html = '';
        $coordinates = explode(';', $this->coords);
        foreach ($coordinates as $coord) {
            $lat_lng = geometry::os_to_lat_long($coord);
            $html .= 'Lat Long: ' . ($lat_lng->lat() > 0 ? 'N' : 'S') . number_format(abs($lat_lng->lat()), 5) . ', ' . ($lat_lng->lng() > 0 ? 'E' : 'W') . number_format(abs($lat_lng->lng()), 5) . '; OS: ' . $coord . '<br/>';
        }
        return $html;
    }

    /**
     * @return string
     */
    public function get_info() {
        if (!isset($this->track)) {
            $this->set_track();
        }
        $html = node::create('table', ['width' => '100'],
            node::create('tr', [], node::create('td', [], 'Flight ID') . node::create('td', [], $this->fid)) .
            node::create('tr', [], node::create('td', [], 'Glider') . node::create('td', [], $this->manufacturer_title . ' - ' . $this->glider_name)) .
            node::create('tr', [], node::create('td', [], 'Club') . node::create('td', [], $this->club_title)) .
            node::create('tr', [], node::create('td', [], 'Defined') . node::create('td', [], get::bool($this->defined))) .
            node::create('tr', [], node::create('td', [], 'Launch') . node::create('td', [], get::launch($this->lid))) .
            node::create('tr', [], node::create('td', [], 'Type') . node::create('td', [], get::flight_type($this->ftid))) .
            node::create('tr', [], node::create('td', [], 'Ridge Lift') . node::create('td', [], get::bool($this->ridge))) .
            node::create('tr', [], node::create('td', [], 'Score') . node::create('td', [], $this->base_score . 'x' . $this->multi . ' =' . $this->score)) .
            node::create('tr', [], node::create('td', [], 'Coordinates') . node::create('td', [], $this->coord_info())) .
            node::create('tr', [], node::create('td', [], 'Launched@') . node::create('td', [], date('H:i:s', $this->track->track_points->first()->time))) .
            node::create('tr', [], node::create('td', [], 'Landed@') . node::create('td', [], date('H:i:s', $this->track->track_points->first()->time))) .
            node::create('tr', [], node::create('td', [], 'Duration') . node::create('td', [], date('H:i:s', $this->track->track_points->last()->time - $this->track->track_points->first()->time))) .
            ($this->vis_info ? node::create('tr', [], node::create('td', [], 'Info') . node::create('td', [], $this->vis_info)) : '') .
            (file_exists(root . '/uploads/flight/' . $this->fid . '/track.kmz') ?
                node::create('tr', [], node::create('td.center.view', ['colspan' => 2], node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flight(' . $this->fid . ')'], 'Add trace to Map'))) .
                node::create('tr', [], node::create('td.center', ['colspan' => 2],
                        node::create('a.download.igc', ['href' => $this->get_download_url('igc'), 'rel' => 'external'], 'Download IGC') .
                        node::create('a.download.kml', ['href' => $this->get_download_url('kmz'), 'rel' => 'external'], 'Download KML')
                    )
                ) :
                node::create('tr', [], node::create('td.center.view.coords', ['colspan' => 2],
                        node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flightC(\'' . $this->coords . '\',' . $this->fid . ');return false;'], 'Add coordinates to map')
                    )
                )
            )
        ) . ajax ? node::create('a.close', ['title' => 'close', 'onclick' => '$("#pop").remove()'], 'Close') : '';

        return $html;
    }

    /**
     *
     */
    public function get_js() {
        if (isset($_REQUEST['id'])) {
            $id = (int) $_REQUEST['id'];
            header("Content-type: application/json");
            die(preg_replace('/\s+/im', ' ', file_get_contents(root . '/uploads/flight/' . ($id > 100000 ? 'temp/' : '') . $id . '/track.js')));
        }
    }

    /**
     * @param string $prefix
     * @return node
     */
    function to_print($prefix = '') {
        if ($this->did == 3) {
            $lead = '&#8801;';
            $i = '.kml';
        } elseif ($this->did == 2) {
            $lead = "&#61;";
            $i = '.kml';
        } else {
            $lead = '&#45;';
            $i = '';
        }
        if ($this->defined)
            $d = ".defined";
        else
            $d = "";
        $b = get::launch_letter($this->lid);
        $b .= round($this->score, 2);
        $type = get::type($this->ftid);
        return node::create('td.' . $type . $d . $i . ' div.wrap a#fid' . $this->fid . '.click' . $this->fid, ['href' => $this->get_url(), 'data-ajax-click' => '\object\flight:get_info_ajax', 'data-ajax-post' => '{"fid":' . $this->fid . '}', 'title' => 'Flight:' . $this->fid], $prefix . $lead . $b);
    }

    /**
     * @return string
     */
    public function get_url() {
        return '/flight_info/' . $this->fid;
    }
}
