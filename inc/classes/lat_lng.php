<?php

namespace classes;

class lat_lng {

    public $ele = 0;
    private $cos_lat;
    private $sin_lat;
    private $lat;
    private $lng;
    private $lat_rad;
    private $lng_rad;

    public function __construct($lat = null, $lng = null) {
        if ($lat !== false) {
            $this->set_lat($lat);
        }
        if ($lng !== false) {
            $this->set_lng($lng);
        }
    }

    /** @return lat_lng_bound */
    public function get_grid_cell() {
        /** @var lat_lng_bound $cell */
        foreach (OS::cells() as $cell) {
            if ($cell->contains($this)) {
                return $cell;
            }
        }
        $bound = new \stdClass();
        $bound->code = 'N/A';
        return $bound;
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

    public function sin_lat() {
        if (!$this->sin_lat) {
            $this->sin_lat = sin($this->lat);
        }
        return $this->sin_lat;
    }

    public function cos_lat() {
        if (!$this->cos_lat) {
            $this->cos_lat = cos($this->lat);
        }
        return $this->cos_lat;
    }

    public function get_coordinate() {
        return geometry::lat_long_to_os($this);
    }

    public function get_dist_to(lat_lng $b) {
        return geometry::get_distance($this, $b);
    }

    public function get_dist_to_precise(lat_lng $b) {
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

 