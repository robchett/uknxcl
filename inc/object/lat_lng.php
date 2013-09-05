<?php

class lat_lng {

    private $lat;
    private $lng;
    private $lat_rad;
    private $lng_rad;

    public function __construct($lat = false, $lng = false) {
        if ($lat !== false) {
            $this->set_lat($lat);
        }
        if ($lng !== false) {
            $this->set_lng($lng);
        }
    }

    public function set_lat($float) {
        $this->lat = $float;
        $this->lat_rad = $float * M_PI / 180;
    }

    public function set_lng($float) {
        $this->lng = $float;
        $this->lng_rad = $float * M_PI / 180;
    }

    public function lat_rad() {
        return $this->lat_rad;
    }

    public function lng_rad() {
        return $this->lng_rad;
    }

    public function lat() {
        return $this->lat;
    }

    public function lng() {
        return $this->lng;
    }

    public function get_coordinate() {
        return geometry::lat_long_to_os($this);
    }

    public function get_dist_to(track_point $b) {
        return geometry::get_distance($this, $b);
    }

    public function get_dist_to_precise(track_point $b) {
        return geometry::get_distance_ellipsoid($this, $b);
    }

    public function get_kml_coordinate($time = null) {
        if ($time !== null) {
            return sprintf("%8f,%8f,%-5d,%6d ", $this->lng(), $this->lat(), $this->ele, $time);
        } else {
            return sprintf("%8f,%8f,%-5d ", $this->lng(), $this->lat(), $this->ele);
        }
    }
}

 