<?php
class flight extends table {
    public $base_score;
    public $club_name;
    public $defined;
    public $did;
    /** @var int Flight ID */
    public $fid;
    /** @var string Date flown */
    public $date;
    public $ftid;
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
    public $coords;
    /** @var int glider class - 1 or 5 for rigid or flex */
    public $class;
    /** @var int The id used for a flight, depends on whether $class is 1 or 5 */
    public $ClassID;

    public static $launch_types = array(0 => 'Foot', 1 => 'Aerotow', 2 => 'Winch');
    public static $module_id = 2;
    public $pilot_name;
    public $ridge;
    public $score;
    public $table_key = 'fid';
    public $time;
    public $vis_info;
    /** @var  bool Season the flight was flown in */
    public $season;

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

    /* @return flight_array */
    public static function get_all(array $fields, array $options = array()) {
        return flight_array::get_all($fields, $options);
    }

    public function download() {
        $id = (int) $_REQUEST['id'];
        $this->do_retrieve(
            array('flight.*', 'pilot.name'),
            array(
                'join' => array('pilot' => 'flight.pid=pilot.pid'),
                'where_equals' => array('flight.fid' => $id)
            )
        );
        if (isset($this->fid) && $this->fid) {
            $fullPath = root . '/uploads/track/' . $id . '/' . ($_REQUEST['type'] == 'kml' ? 'track_earth.kml' : 'track.igc');
            if ($fd = fopen($fullPath, "r")) {
                $fsize = filesize($fullPath);
                header("Content-type: application/octet-stream");
                header('Content-Disposition: filename="' . $id . '-' . str_replace(' ', '_', $this->pilot_name) . '-' . $this->date . '.' . $_REQUEST['type'] . '"');
                header("Content-length: $fsize");
                header("Cache-control: private");
                while (!feof($fd)) {
                    $buffer = fread($fd, 2048);
                    echo $buffer;
                }
            }
            fclose($fd);
        }
    }

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

    public function get_multiplier($type = null, $season = null) {
        if (!$this->ridge) {
            return flight_type::get_multiplier(isset($type) ? $type : $this->ftid, isset($season) ? $season : $this->season, $this->ridge);
        } else {
            return 1;
        }
    }

    public function get_statistics() {
        $year_stats = array();
        foreach (range(1991, 2013) as $key => $year) {
            $months = array();
            foreach (range(1, 12) as $key2 => $month) {
                $score = db::result('SELECT sum(score) AS score FROM flight WHERE YEAR(date) = :year AND MONTH(date) = :month', array('year' => $year, 'month' => $month))->score;
                $tot = db::result('SELECT count(fid) AS count FROM flight WHERE YEAR(date) = :year AND MONTH(date) = :month', array('year' => $year, 'month' => $month))->count;
                $months[$key2] = array($score, $tot);
            }
            $year_stats[$key] = $months;
        }
        echo json_encode($year_stats);
        die();
    }

    public function generate_benchmark() {
        $flights = flight::get_all(array(), array('where' => 'did > 1 AND season = 2012 AND ftid != 3 AND fid>=8946', 'order' => 'fid DESC'));
        $total_time = 0;
        //$flights->iterate(function (flight $flight) use (&$total_time) {
        /** @var flight $flight */
        foreach ($flights as $flight) {
            $track = new track();
            $track->time = 0;
            $time = time();
            echo '<p> Track :' . $flight->fid . '</p>';
            if ($flight->ftid != 3 && $track->generate($flight)) {
                $time = time() - $time;
                $total_time += $time;
                $flight->time = $time;
                $best_score = $flight->get_best_score();
                switch ($flight->ftid) {
                    case  1:
                        if ($track->od->get_distance() > $flight->base_score) {
                            echo '<span style="color:#00ff00">OD Gained ' . ($track->od->get_distance() - $flight->base_score) . 'km (' . ($track->od->get_distance() / ($track->od->get_distance() - $flight->base_score * 100)) . ')' . '</span><br/>';
                        } else {
                            echo '<span style="color:#ff0000">OD Lost ' . ($flight->base_score - $track->od->get_distance()) . 'km (' . ($track->od->get_distance() / ($track->od->get_distance() - $flight->base_score * 100)) . ')' . '</span><br/>';
                        }
                        break;
                    case  2:
                        if ($track->or->get_distance() > $flight->base_score) {
                            echo '<span style="color:#00ff00">OR Gained ' . ($track->or->get_distance() - $flight->base_score) . 'km (' . ($track->or->get_distance() / ($track->or->get_distance() - $flight->base_score * 100)) . ')' . '</span><br/>';
                        } else {
                            echo '<span style="color:#ff0000">OR Lost ' . ($flight->base_score - $track->or->get_distance()) . 'km (' . ($track->or->get_distance() / ($track->or->get_distance() - $flight->base_score * 100)) . ')' . '</span><br/>';
                        }
                        break;
                    case  3:
                        break;
                    case  4:
                        if ($track->tr->get_distance() > $flight->base_score) {
                            echo '<span style="color:#00ff00">TR Gained ' . ($track->tr->get_distance() - $flight->base_score) . 'km (' . ($track->tr->get_distance() / ($track->tr->get_distance() - $flight->base_score * 100)) . ')' . '</span><br/>';
                        } else {
                            echo '<span style="color:#ff0000">TR Lost ' . ($flight->base_score - $track->tr->get_distance()) . 'km (' . ($track->tr->get_distance() / ($track->tr->get_distance() - $flight->base_score * 100)) . ')' . '</span><br/>';
                        }
                        break;
                    case  5:
                        if ($track->ft->get_distance() > $flight->base_score) {
                            echo '<span style="color:#00ff00">TR Gained ' . ($track->ft->get_distance() - $flight->base_score) . 'km (' . ($track->ft->get_distance() / ($track->ft->get_distance() - $flight->base_score * 100)) . ')' . '</span><br/>';
                        } else {
                            echo '<span style="color:#ff0000">TR Lost ' . ($flight->base_score - $track->ft->get_distance()) . 'km (' . ($track->ft->get_distance() / ($track->ft->get_distance() - $flight->base_score * 100)) . ')' . '</span><br/>';
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
                $flight->do_save();
            } else {
                $flight->time = 0;
                echo '<p> Track :' . $flight->fid . ' failed to calculate</p>';
            }
            flush();
        }
        //);

        $flights->uasort(function ($a, $b) {
                return $b->time - $a->time;
            }
        );

        foreach ($flights as $flight) {
            echo '<p> Track :' . $flight->fid . ' scored in ' . date('H:i:s', $flight->time) . '</p>';
        }

        $average_time = $total_time / count($flights);

        echo '<p> Average Time :' . date('H:i:s', $average_time) . '</p>';
    }

    public function generate_files() {
        if (isset($_REQUEST['id'])) {
            $this->do_retrieve_from_id(array(), $_REQUEST['id']);
            if ($this->fid) {
                $track = new track();
                $track->generate($this);
                $this->do_save();
                jquery::colorbox(array('html' => 'Flight ' . $this->fid . ' generated successfully.<p><pre>' . print_r($track->log_file, 1) . '</pre></p>'));
            }
        }
    }

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
            $html = $this->get_info();
        }
        ajax::inject('#' . $_REQUEST['origin'], 'after', '<script>$("#pop").remove();</script>');
        ajax::inject('#' . $_REQUEST['origin'], 'after', '<div id="pop"><span class="arrow">Arrow</span><div class="content">' . $html . '</div><script>if($("#pop").offset().left > 400)$("#pop").addClass("reverse"); </script></div>');
    }

    public function get_stats() {
        $track = new track();
        $track->id = $this->fid;
        $track->parse_IGC();
        $track->trim();
        $track->get_graph_values();
        $height = $track->get_stats('ele');
        $speed = $track->get_stats('speed');
        $climb = $track->get_stats('climbRate');
        $html = '
<table class="stats">
    <thead>
        <tr>
            <th></th>
            <th>Min</th>
            <th>Max</th>
            <th>Average</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Elevation</td>
            <td>' . $height->min . 'ft</td>
            <td>' . $height->max . 'ft</td>
            <td>' . number_format($height->average, 2) . 'ft</td>
        </tr>
        <tr>
            <td>Speed</td>
            <td>' . $speed->min . 'km/h</td>
            <td>' . $speed->max . 'km/h</td>
            <td>' . number_format($speed->average, 2) . 'km/h</td>
        </tr>
        <tr>
            <td>Climb</td>
            <td>' . $climb->min . 'ft/s</td>
            <td>' . $climb->max . 'ft/s</td>
            <td>' . number_format($climb->average, 2) . 'ft/s</td>
        </tr>
    </tbody>
</table>';
        return $html;
    }

    public function get_info() {
        $html = '  <table width="100%">
            <tr><td>Flight ID </td><td>' . $this->fid . '</td></tr>
            <tr><td>Pilot </td><td>' . $this->pilot_name . '</td></tr>
            <tr><td>Date </td><td>' . $this->date . '</td></tr>
            <tr><td>Glider </td><td>' . $this->manufacturer_title . ' - ' . $this->glider_name . '</td></tr>
            <tr><td>Club </td><td>' . $this->club_title . '</td></tr>
            <tr><td>Defined </td><td>' . get::bool($this->defined) . '</td></tr>
            <tr><td>Launch </td><td>' . get::launch($this->lid) . '</td></tr>
            <tr><td>Type </td><td>' . get::flight_type($this->ftid) . '</td></tr>
            <tr><td>Ridge Lift </td><td>' . get::bool($this->ridge) . ' </td></tr>
            <tr><td>Score </td><td>' . $this->base_score . 'x' . $this->multi . ' =' . $this->score . '</td></tr>
            <tr><td>Coordinates </td><td>' . str_replace(';', '; ', $this->coords) . '</td></tr>
            <tr><td>Info</td><td>' . $this->vis_info . '</td></tr>';

        if (file_exists(root . '/uploads/track/' . $this->fid . '/track.kmz')) {
            $html .= '
            <tr><td colspan="2" class="center view"><a href="#" class="button" onclick="map.add_flight(' . $this->fid . ')">Add trace to Map</a></td></tr>
            <tr>
                <td class="center" colspan="2">
                    <a href="/?module=flight&amp;act=download&amp;type=igc&amp;id=' . $this->fid . '" title="Download IGC" class="download igc">Download IGC</a>
                    <a href="/?module=flight&amp;act=download&amp;type=kml&amp;id=' . $this->fid . '" title="Download KML" class="download kml">Download KML</a>
                </td>
            </tr>';
        } else {
            $html .= '<tr><td colspan="2"class="center view coords"><a href="#" class="button" onclick="map.add_flightC(\'' . $this->coords . '\',' . $this->fid . ');return false;"> Add coordinates to map<a/></td></tr>';
        }
        if (ajax) {
            $html .= '<a class="close" title="close" onclick="$(\'#pop\').remove()">Close</a>';
        }
        $html .= '</table>';
        return $html;
    }

    public function get_js() {
        if (isset($_REQUEST['id'])) {
            $id = (int) $_REQUEST['id'];
            header("Content-type: application/json");
            die(preg_replace('/\s+/im', ' ', file_get_contents(root . '/uploads/track/' . ($id > 100000 ? 'temp/' : '') . $id . '/track.js')));
        }
    }

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
        return html_node::create('td.' . $type . $d . $i . ' div.wrap', html_node::inline('a#fid' . $this->fid . '.click' . $this->fid, $prefix . $lead . $b, array('href' => $this->get_url(), 'data-ajax-click' => 'flight:get_info_ajax', 'data-ajax-post' => '{"fid":' . $this->fid . '}', 'title' => 'Flight:' . $this->fid)));
    }

    public function get_url() {
        return '/flight_info/' . $this->fid;
    }
}

class flight_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input);
    }

    /* @return flight */
    public function next() {
        return parent::next();
    }
}

class flight_iterator extends table_iterator {

    /* @return flight */
    public function key() {
        return parent::key();
    }
}