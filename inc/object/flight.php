<?php
class flight extends table {

    public static $launch_types = array(0 => 'Foot', 1 => 'Aerotow', 2 => 'Winch');
    public static $module_id = 2;
    public $table_key = 'fid';

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
            $fullPath = root . '/uploads/track/' . $id . '/' . ($_REQUEST['type'] == 'kml' ? 'Track_Earth.kml' : 'track.igc');
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

    public function generate_benchmark() {
        $flights = flight::get_all(array(), array('where' => 'did > 1', 'limit' => 20, 'order' => 'fid DESC'));
        $total_time = 0;
        $flights->iterate(function (flight $flight) use (&$total_time) {
                $track = new track();
                $track->time = 0;
                $time = time();
                echo '<p> Track :' . $flight->fid . '</p>';
                if ($track->generate($flight)) {
                    $time = time() - $time;
                    $total_time += $time;
                    $flight->time = $time;
                    $flight->do_save();
                } else {
                    echo '<p> Track :' . $flight->fid . ' failed to calculate</p>';
                }
                flush();
            }
        );

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

    /* @return flight_array */
    public static function get_all(array $fields, array $options = array()) {
        return flight_array::get_all($fields, $options);
    }

    public function generate_files() {
        if (isset($_POST['id'])) {
            $this->do_retrieve_from_id(array(), $_POST['id']);
            if ($this->fid) {
                $track = new track();
                $track->generate($this);
                $this->do_save();
                jquery::colorbox(array('html' => 'Flight ' . $this->fid . ' generated successfully.<p><pre>' . print_r($track->log_file, 1) . '</pre></p>'));
            }
        }
    }

    public function get_info() {
        $html = '';
        $id = (int) $_REQUEST['fid'];
        $this->do_retrieve(
            array('flight.*', 'pilot.name', 'club.name', 'glider.name', 'manufacturer.title'),
            array(
                'join' => array(
                    'glider' => 'flight.gid=glider.gid',
                    'club' => 'flight.cid=club.cid',
                    'pilot' => 'flight.pid=pilot.pid',
                    'manufacturer' => 'glider.mid=manufacturer.mid'
                ),
                'where_equals' => array('flight.fid' => $id)
            )
        );
        if (!isset($this->fid) || !$this->fid) {
            $html .= 'Flight not found, this is a bug...';
        }
        $html .= '  <table width="100%">
            <tr><td>Flight ID </td><td>' . $id . '</td></tr>
            <tr><td>Pilot </td><td>' . $this->pilot_name . '</td></tr>
            <tr><td>Date </td><td>' . $this->date . '</td></tr>
            <tr><td>Glider </td><td>' . $this->manufacturer_title . ' - ' . $this->glider_name . '</td></tr>
            <tr><td>Club </td><td>' . $this->club_name . '</td></tr>
            <tr><td>Defined </td><td>' . get::bool($this->defined) . '</td></tr>
            <tr><td>Launch </td><td>' . get::launch($this->lid) . '</td></tr>
            <tr><td>Type </td><td>' . get::flight_type($this->ftid) . '</td></tr>
            <tr><td>Ridge Lift </td><td>' . get::bool($this->ridge) . ' </td></tr>
            <tr><td>Score </td><td>' . $this->base_score . 'x' . $this->multi . ' =' . $this->score . '</td></tr>
            <tr><td>Coordinates </td><td>' . str_replace(';', '; ', $this->coords) . '</td></tr>
            <tr><td>Info</td><td>' . $this->vis_info . '</td></tr>';

        if (file_exists(root . '/uploads/track/' . $id . '/track.kml')) {
            $html .= '
            <tr><td colspan="2" class="center"><a href="#" class="button" onclick="map.add_flight(' . $id . ')">Add trace to Map</a></td></tr>
            <tr>
                <td class="center" colspan="2">
                    <a href="/?module=flight&amp;act=download&amp;type=igc&amp;id=' . $id . '" title="Download IGC" class="download igc">Download IGC</a>
                    <a href="/?module=flight&amp;act=download&amp;type=kml&amp;id=' . $id . '" title="Download KML" class="download kml">Download KML</a>
                </td>
            </tr>';
        } else {
            $html .= '<tr><td colspan="2" class="center"><input type="submit" onclick="map.add_flightC(\'' . $this->coords . '\',' . $id . ')" value="Add coordinates to map"/></td></tr>';
        }

        $html .= '</table>';
        $html .= '<a class="close" title="close" onclick="$(\'#pop\').remove()">Close [x]</a>';
        ajax::inject('#' . $_REQUEST['origin'], 'after', '<script>$("#pop").remove();</script>');
        ajax::inject('#' . $_REQUEST['origin'], 'after', '<div id="pop"><span class="arrow">Arrow</span><div class="content">' . $html . '</div><script>if($("#pop").offset().left > 400)$("#pop").addClass("reverse"); </script></div>');
    }

    public function get_js() {
        if (isset($_REQUEST['id'])) {
            $id = (int) $_REQUEST['id'];
            header("Content-type: application/json");
            die(preg_replace('/\s+/im', ' ', file_get_contents(root . 'uploads/track/' . $id . '/Track.js')));
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
        return html_node::create('td.' . $type . $d . $i, html_node::inline('a#fid' . $this->fid . '.click' . $this->fid, $prefix . $lead . $b, ['data-ajax-click' => 'flight:get_info', 'data-ajax-post' => '{"fid":' . $this->fid . '}', 'title' => 'Flight:' . $this->fid]));
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