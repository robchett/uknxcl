<?php
namespace track;

use classes\lat_lng;

class track_point extends lat_lng {

    public $alt = 0;
    public $bearing;
    public $bestBack;
    public $bestFwrd;
    public $climbRate = 0;
    public $ele = 0;
    public $id = 0;
    public $speed = 0;
    public $time = 0;
    public $val = 0;

    public function get_graph_point($time = 0) {
        $coordinate = [$time, $this->ele, $this->climbRate, $this->speed, $this->bearing];
        return $coordinate;
    }

    public function get_js_coordinate() {
        $coordinate = new \stdClass();
        $coordinate->ele = $this->ele;
        $coordinate->lat = $this->lat();
        $coordinate->lng = $this->lng();
        return $coordinate;
    }

    public function get_kml_point() {
        $xml = '<Point><altitudeMode>absolute</altitudeMode><coordinates>' . $this->get_kml_coordinate() . '</coordinates></Point>';
        return $xml;
    }

    public function get_time_to(track_point $b) {
        return $b->time - $this->time;
    }
}