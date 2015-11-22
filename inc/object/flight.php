<?php

namespace object;

use classes\ajax;
use classes\db;
use classes\geometry;
use classes\get;
use classes\jquery;
use classes\table;
use html\node;
use track\track;
use traits\table_trait;

/**
 * @property mixed club_title
 * @property mixed winter
 * @property mixed delayed
 */
class flight extends table {
    use table_trait;

    public $title;
    public $added;
    public $info;
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
    public static $launch_types = [0 => 'Foot', 1 => 'Aerotow', 2 => 'Winch'];
    public $pilot_name;
    public $ridge;
    public $score;
    public $time;
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

    public $go_score;
    public $go_time;
    public $go_coordinates;
    public $go_type;

    public static $default_joins = [
        'pilot' => 'flight.pid = pilot.pid',
        'glider' => 'flight.gid = glider.gid',
        'club' => 'flight.cid = club.cid',
        'manufacturer' => 'glider.mid = manufacturer.mid'
    ];
    public static $default_fields = [
        'flight.*',
        'pilot.name',
        'club.title',
        'glider.name',
        'manufacturer.title AS manufacturer_title'
    ];
    public $go_distance;


    /**
     *
     */
    public function download() {
        $id = (int) $_REQUEST['id'];
        $this->do_retrieve(
            ['flight.*', 'pilot.name'],
            [
                'join' => ['pilot' => 'flight.pid=pilot.pid'],
                'where_equals' => ['flight.fid' => $id]
            ]
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
                header('Content-Disposition: filename="' . $id . (!isset($_REQUEST['temp']) ? '-' . str_replace(' ', '_', $this->pilot->name) . '-' . $this->date : '') . '.kml"');
                echo $file;
                zip_close($zip);
                return;
            }
            if ($fullPath && $fd = fopen($fullPath, "r")) {
                $fsize = filesize($fullPath);
                header('Content-Disposition: filename="' . $id . '-' . str_replace(' ', '_', $this->pilot->name) . '-' . $this->date . '.' . $_REQUEST['type'] . '"');
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
        $scores = [
            [$this->od_score * $this->get_multiplier(flight_type::OD_ID, $this->season), flight_type::OD_ID],
            [$this->or_score * $this->get_multiplier(flight_type::OR_ID, $this->season), flight_type::OR_ID],
            [$this->tr_score * $this->get_multiplier(flight_type::TR_ID, $this->season), flight_type::TR_ID],
            [$this->ft_score * $this->get_multiplier(flight_type::FT_ID, $this->season), flight_type::FT_ID],
        ];
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

        $flights = new \stdClass();
        $flights->min = 0;
        $flights->max = 0;
        $flights->draw_graph = true;
        $flights->colour = get::js_colour(0);
        $flights->name = 'Total Flights';

        $scores = new \stdClass();
        $scores->min = 0;
        $scores->max = 0;
        $scores->draw_graph = true;
        $scores->colour = get::js_colour(1);
        $scores->name = 'Total Score';

        foreach (range(1991, date('Y')) as $year) {
            $year_object = new \stdClass();
            $year_object->data = [];
            $year_object->min_flights = $year_object->min_score = $year_object->max_score = $year_object->max_flights = 0;
            $year_object->min_score = 0;
            $year_object->draw_graph = true;
            $year_object->colour = get::js_colour($year);
            $year_object->name = $year;

            $total_score = 0;
            $total_flights = 0;

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
                $year_object->coords[] = [$month - 1, $tot, $score];
                $year_object->max_flights = max($year_object->max_flights, $tot);
                $year_object->max_score = max($year_object->max_score, $score);
                $total_score += $score;
                $total_flights += $tot;
            }
            $flights->coords[] = [$year - 1991, $total_flights * 40];
            $flights->max = max($flights->max, $total_flights);
            $scores->coords[] = [$year - 1991, $total_score];
            $scores->max = max($scores->max, $total_score);
            $year_stats[] = $year_object;
        }

        $wrapper = new \stdClass();
        $inner = new \stdClass();
        $inner->track = $year_stats;
        $inner->xMin = 1;
        $inner->xMax = 12;
        $wrapper->nxcl_data = $inner;

        $return['month'] = $wrapper;

        $wrapper = new \stdClass();
        $inner = new \stdClass();
        $inner->track = [$flights, $scores];
        $inner->xMin = 1991;
        $inner->xMax = date('Y');
        $wrapper->nxcl_data = $inner;

        $return['year'] = $wrapper;

        return $return;
    }

    public function get_flights_by_area() {
        $range = db::select('flight')->retrieve(['MAX(date) AS max', 'MIN(date) AS min'])->filter('did > 1 AND (os_codes LIKE "%SU%" OR os_codes LIKE "%TQ%")')->set_limit(1)->execute()->fetch();
        echo print_r($range);
        $flights = flight::get_all([], ['where' => 'did > 1 AND (os_codes LIKE "%SU%" OR os_codes LIKE "%TQ%")', 'order' => 'fid DESC']);
        echo count($flights);
        $flights->iterate(function (flight $flight, $cnt) {
                $track = new track($flight->fid);
                $path = $track->get_kmz_raw();
                if (file_exists($path)) {
                    copy($path, root . '/tmp/' . $cnt . '.kmz');
                }
            }
        );
    }

    /**
     * @param int|null $fid
     */
    public static function generate_benchmark($fid = null) {
        $log = new log(log::DEBUG, '/.cache/track_generation.log');
        $options = ['where' => 'did > 1', 'order' => 'fid ASC'];
        if ($fid) {
            $options['where'] .= ' AND fid >=' .$fid;
        }
        $flights = flight::get_all([], $options);
        set_time_limit(0);
        $total_time = 0;
        $flights->iterate(
            function (flight $flight) use (&$total_time, &$log) {
                $track = new track($flight->fid);
                $track->set_flight($flight);
                $track->time = 0;
                $time = time();
                $log->info('Track: ' . $flight->fid);
                $log->debug('---- Reading');
                if(!file_exists($track->get_igc())) {
                    if(!is_dir($track->get_file_loc())) {
                        mkdir($track->get_file_loc());
                    }
                    copy('/var/www/vhosts/uknxcl.old/web/Tracks/' . $flight->fid . '/Track_log.igc', $track->get_file_loc() . '/track.igc');
                }
                if ($track->parse_IGC()) {
                    $log->debug('---- Read');
                    $codes = [];
                    /** @var \classes\lat_lng $point */
                    foreach ($track->coordinate_set as $point) {
                        $codes[] = $point->get_grid_cell()->code;
                        $codes = array_unique($codes);
                    }
                    $flight->os_codes = implode(',', $codes);
                    $log->debug('---- OS Areas');
                    $track->generate();
                    $flight->do_save();
                    $log->debug('---- Generated');
//                    $best_score = $flight->get_best_score();
//                    switch ($flight->ftid) {
//                        case  1:
//                            if ($track->od->get_distance() > $flight->base_score) {
//                                echo node::create('span', ['style' => 'color:#00ff00'], 'OD Gained ' . ($track->od->get_distance() - $flight->base_score) . 'km (' . ($track->od->get_distance() / ($track->od->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
//                            } else {
//                                echo node::create('span', ['style' => 'color:#ff0000'], 'OD Lost ' . ($flight->base_score - $track->od->get_distance()) . 'km (' . ($track->od->get_distance() / ($track->od->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
//                            }
//                            break;
//                        case
//                        2:
//                            if ($track->or->get_distance() > $flight->base_score) {
//                                echo node::create('span', ['style' => 'color:#00ff00'], 'OR Gained ' . ($track->or->get_distance() - $flight->base_score) . 'km (' . ($track->or->get_distance() / ($track->or->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
//                            } else {
//                                echo node::create('span', ['style' => 'color:#ff0000'], 'OR Lost ' . ($flight->base_score - $track->or->get_distance()) . 'km (' . ($track->or->get_distance() / ($track->or->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
//                            }
//                            break;
//                        case  3:
//                            break;
//                        case  4:
//                            if ($track->tr->get_distance() > $flight->base_score) {
//                                echo node::create('span', ['style' => 'color:#00ff00'], 'TR Gained ' . ($track->tr->get_distance() - $flight->base_score) . 'km (' . ($track->tr->get_distance() / ($track->tr->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
//                            } else {
//                                echo node::create('span', ['style' => 'color:#ff0000'], 'TR Lost ' . ($flight->base_score - $track->tr->get_distance()) . 'km (' . ($track->tr->get_distance() / ($track->tr->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
//                            }
//                            break;
//                        case  5:
//                            if ($track->ft->get_distance() > $flight->base_score) {
//                                echo node::create('span', ['style' => 'color:#00ff00'], 'TR Gained ' . ($track->ft->get_distance() - $flight->base_score) . 'km (' . ($track->ft->get_distance() / ($track->ft->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
//                            } else {
//                                echo node::create('span', ['style' => 'color:#ff0000'], 'TR Lost ' . ($flight->base_score - $track->ft->get_distance()) . 'km (' . ($track->ft->get_distance() / ($track->ft->get_distance() - $flight->base_score * 100)) . ')') . '<br/>';
//                            }
//                            break;
//                    }
//                    if ($flight->ftid != $best_score[1]) {
//                        echo 'Flight Scored better as a' . $best_score[1];
//                        switch ($best_score[0]) {
//                            case  flight_type::OD_ID:
//                                $flight->coords = $track->od->get_coordinates();
//                                $flight->base_score = $track->od->get_distance();
//                                break;
//                            case  flight_type::OR_ID:
//                                $flight->coords = $track->or->get_coordinates();
//                                $flight->base_score = $track->or->get_distance();
//                                break;
//                            case  flight_type::TR_ID:
//                                $flight->coords = $track->tr->get_coordinates();
//                                $flight->base_score = $track->tr->get_distance();
//                                break;
//                            case  flight_type::FT_ID:
//                                $flight->coords = $track->ft->get_coordinates();
//                                $flight->base_score = $track->ft->get_distance();
//                                break;
//                        }
//                        $flight->score = $best_score[0];
//                        $flight->ftid = $best_score[1];
//                        $flight->multi = $flight->get_multiplier();
//                    }
//                    $flight->do_save();
                    $time = time() - $time - 60 * 60;
                    $total_time += $time;
                    $flight->time = $time;
                } else {
                    $flight->time = 0;
                    $log->warning('---- Could not be read');
                }
            }
        );

        $flights->uasort(function ($a, $b) {
                return $b->time - $a->time;
            }
        );

        foreach ($flights as $flight) {
            $log->info('Track :' . $flight->fid . ' scored in ' . date('H:i:s', $flight->time));
        }

        $average_time = $total_time / count($flights);

        $log->info('Average Time :' . date('H:i:s', $average_time));
    }

    /**
     *
     */
    public function generate_files() {
        if (isset($_REQUEST['id'])) {
            $this->do_retrieve_from_id([], $_REQUEST['id']);
            if ($this->fid) {
                $track = new track($this->fid);
                $track->set_flight($this);
                $track->generate();
                $this->do_save();
                jquery::colorbox(['html' => 'Flight ' . $this->fid . ' generated successfully. ' . node::create('p pre', [], print_r($track->log_file, 1))]);
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
            [
                'join' => array_merge(
                    self::$default_joins,
                    ['manufacturer' => 'glider.mid=manufacturer.mid']
                ),
                'where_equals' => ['flight.fid' => $id]
            ]
        );
        if (!isset($this->fid) || !$this->fid) {
            $html .= 'Flight not found, this is a bug...';
        } else {
            $html = node::create('table', ['width' => '100%'],
                node::create('tr', [], node::create('td', [], 'Flight ID') . node::create('td', [], $this->fid)) .
                node::create('tr', [], node::create('td', [], 'Pilot') . node::create('td', [], $this->pilot->name)) .
                node::create('tr', [], node::create('td', [], 'Date') . node::create('td', [], date('d/m/Y', $this->date))) .
                node::create('tr', [], node::create('td', [], 'Glider') . node::create('td', [], $this->manufacturer_title . ' - ' . $this->glider->name)) .
                node::create('tr', [], node::create('td', [], 'Club') . node::create('td', [], $this->club->title)) .
                node::create('tr', [], node::create('td', [], 'Defined') . node::create('td', [], get::bool($this->defined))) .
                node::create('tr', [], node::create('td', [], 'Launch') . node::create('td', [], get::launch($this->lid))) .
                node::create('tr', [], node::create('td', [], 'Type') . node::create('td', [], get::flight_type($this->ftid))) .
                node::create('tr', [], node::create('td', [], 'Ridge Lift') . node::create('td', [], get::bool($this->ridge))) .
                node::create('tr', [], node::create('td', [], 'Score') . node::create('td', [], $this->base_score . 'x' . $this->multi . ' = ' . $this->score)) .
                node::create('tr', [], node::create('td', [], 'Coordinates') . node::create('td', [], str_replace(';', '; ', $this->coords))) .
                node::create('tr', [], node::create('td', [], 'Info') . node::create('td.info', [], $this->info)) .

                (file_exists(root . '/uploads/flight/' . $this->fid . '/track.kmz') ?
                    node::create('tr td.center.view', ['colspan' => 2], node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flight(' . $this->fid . ')'], 'Add trace to Map')) .
                    node::create('tr td.center.view', ['colspan' => 2],
                        node::create('a.download.igc', ['href' => $this->get_download_url('igc'), 'title' => "Download IGC", 'rel' => 'external'], 'Download IGC') .
                        node::create('a.download.kml', ['href' => $this->get_download_url('kmz'), 'title' => 'Download KML', 'rel' => 'external'], 'Download KML')
                    ) :
                    node::create('tr td.center.view.coords', ['colspan' => 2], node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flight_coordinates(\'' . $this->coords . '\',' . $this->fid . ');return false;'], node::create('span.glyphicon.glyphicon-pushpin', [], '') . 'Add coordinates to map'))
                ) .
                node::create('a.close.glyphicon.glyphicon-remove', ['title' => 'close', 'onclick' => '$("#pop").remove()'], '')
            );
        }

        ajax::add_script('$("#pop").remove();', true);
        ajax::inject('#' . $_REQUEST['origin'], 'after',
            node::create('div#pop.callout.callout-primary', [],
                node::create('span.arrow', [], 'Arrow') .
                node::create('div.content', [], $html)
            )
        );
        ajax::add_script('if($("#pop").offset().left > 400)$("#pop").addClass("reverse");');
    }

    protected function  get_download_url($type = 'igc') {
        return '/?module=\object\flight&act=download&id=' . $this->fid . '&type=' . $type;
    }

    /**
     *
     */
    private function set_track() {
        $track = new track($this->fid);
        $this->track = $track;
        if ($track->parse_IGC()) {
            $track->coordinate_set->trim();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function get_stats() {
        if (!isset($this->track)) {
            $this->set_track();
        }
        $this->track->coordinate_set->set_graph_values();
        $height = $this->track->coordinate_set->stats()->height();
        $speed = $this->track->coordinate_set->stats()->speed();
        $climb = $this->track->coordinate_set->stats()->climb();
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
                    node::create('td', [], $height->min() . 'ft <span>@ ' /*. date('H:i:s', $height->min_point->time)*/) .
                    node::create('td', [], $height->max() . 'ft <span>@ ' /*. date('H:i:s', $height->max_point->time)*/) .
                    node::create('td', [], number_format(/*$height->average*/0, 2) . 'm')
                ) .
                node::create('tr', [],
                    node::create('td', [], 'Speed') .
                    node::create('td', [], $speed->min() . 'km/h <span>@ ' /*. date('H:i:s', $speed->min_point->time)*/) .
                    node::create('td', [], $speed->max() . 'km/h <span>@ ' /*. date('H:i:s', $speed->max_point->time)*/) .
                    node::create('td', [], number_format(/*$speed->average*/0, 2) . 'km/h')
                ) .
                node::create('tr', [],
                    node::create('td', [], 'Speed') .
                    node::create('td', [], $climb->min() . 'ft/s <span>@ ' /*. date('H:i:s', $climb->min_point->time)*/) .
                    node::create('td', [], $climb->max() . 'ft/s <span>@ ' /*. date('H:i:s', $climb->max_point->time)*/) .
                    node::create('td', [], number_format(/*$climb->average*/0, 2) . 'ft/s')
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
        if ($this->did > 1 && !isset($this->track) && $this->set_track()) {
            $logged_data =
                node::create('tr', [], node::create('td', [], 'Launched@') . node::create('td', [], date('H:i:s', $this->track->coordinate_set->first()->timestamp()))) .
                node::create('tr', [], node::create('td', [], 'Landed@') . node::create('td', [], date('H:i:s', $this->track->coordinate_set->last()->timestamp()))) .
                node::create('tr', [], node::create('td', [], 'Duration') . node::create('td', [], date('H:i:s', $this->track->coordinate_set->last()->timestamp() - $this->track->coordinate_set->first()->timestamp())));
        } else {
            $logged_data = '';
        }
        $html = node::create('table', ['width' => '100%'],
            node::create('tr', [], node::create('td', [], 'Flight ID') . node::create('td', [], $this->fid)) .
            node::create('tr', [], node::create('td', [], 'Glider') . node::create('td', [], $this->manufacturer_title . ' - ' . $this->glider->name)) .
            node::create('tr', [], node::create('td', [], 'Club') . node::create('td', [], $this->club->title)) .
            node::create('tr', [], node::create('td', [], 'Defined') . node::create('td', [], get::bool($this->defined))) .
            node::create('tr', [], node::create('td', [], 'Launch') . node::create('td', [], get::launch($this->lid))) .
            node::create('tr', [], node::create('td', [], 'Type') . node::create('td', [], get::flight_type($this->ftid))) .
            node::create('tr', [], node::create('td', [], 'Ridge Lift') . node::create('td', [], get::bool($this->ridge))) .
            node::create('tr', [], node::create('td', [], 'Score') . node::create('td', [], $this->base_score . 'x' . $this->multi . ' =' . $this->score)) .
            node::create('tr', [], node::create('td', [], 'Coordinates') . node::create('td', [], $this->coord_info())) .
            $logged_data .
            ($this->info ? node::create('tr', [], node::create('td', [], 'Info') . node::create('td', [], $this->info)) : '') .
            (file_exists(root . '/uploads/flight/' . $this->fid . '/track.kmz') ?
                node::create('tr', [], node::create('td.center.view', ['colspan' => 2], node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flight(' . $this->fid . ')'], 'Add trace to Map'))) .
                node::create('tr', [], node::create('td.center', ['colspan' => 2],
                        node::create('a.download.igc', ['href' => $this->get_download_url('igc'), 'rel' => 'external'], 'Download IGC') .
                        node::create('a.download.kml', ['href' => $this->get_download_url('kmz'), 'rel' => 'external'], 'Download KML')
                    )
                ) :
                node::create('tr', [], node::create('td.center.view.coords', ['colspan' => 2],
                        node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flight_coordinates(\'' . $this->coords . '\',' . $this->fid . ');return false;'], 'Add coordinates to map')
                    )
                )
            )
        ) . (ajax ? node::create('a.close', ['title' => 'close', 'onclick' => '$("#pop").remove()'], 'Close') : '');

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
