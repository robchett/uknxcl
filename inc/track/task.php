<?php
namespace track;

use classes\geometry;

class task {

    public $_temp_distance = 0;
    public $coordinates;
    public $distance;
    public $ftid;
    public $timestamp;
    public $title;
    public $type;
    /** @var track_point_collection */
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
        /** @var track_point $point */
        foreach ($this->waypoints as $key => $point) {
            $distance = round(($last ? $point->get_dist_to($last) : 0), 2);
            $tot += $distance;
            $html .= '
                <tr>
                    <td>' . $key . '</td>
                    <td>' . $point->lat() . '</td>
                    <td>' . $point->lng() . '</td>
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
        $this->waypoints = new track_point_collection();
        $coordinates = explode(';', $this->coordinates);
        foreach ($coordinates as $coord) {
            //list($coord, $ele) = explode(':', $coord);
            $latlng = geometry::os_to_lat_long($coord);
            $track_point = new track_point($latlng->lat(), $latlng->lng());
            //$track_point->ele = $ele;
            $this->waypoints[] = $track_point;
        }
    }

    public function set($indexes) {
        $this->distance = null;
        $this->waypoints = new track_point_collection();
        foreach ($indexes as $track_point) {
            $this->waypoints[] = $track_point;
        }
        $this->timestamp = $this->waypoints->last()->time - $this->waypoints->first()->time;
    }


}