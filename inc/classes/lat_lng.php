<?php

namespace classes;

class lat_lng {

    public $lat;
    public $lng;
    public $ele;

    public function __construct($lat, $lng) {
        $this->lat = $lat;
        $this->lng = $lng;
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

    public function get_coordinate() {
        return geometry::lat_long_to_os($this);
    }

    public function get_kml_coordinate($time = null) {
        if ($time !== null) {
            return sprintf("%8f,%8f,%-5d,%6d ", $this->lng(), $this->lat(), $this->ele(), $time);
        } else {
            return sprintf("%8f,%8f,%-5d ", $this->lng(), $this->lat(), $this->ele());
        }
    }

    /**
     * @param bool $as_rad *
     *
     * @return float
     */
    public function lat($as_rad = false) {
        return $this->lat * ($as_rad ? M_PI / 180 : 1);
    }

    /**
     * @param bool $as_rad *
     *
     * @return float
     */
    public function lng($as_rad = false) {
        return $this->lng * ($as_rad ? M_PI / 180 : 1);
    }

    /** @return int */
    public function ele() {
        return $this->ele;
    }

    public function sin_lat() {
        return sin($this->lat(true));
    }

    public function sin_lng() {
        return sin($this->lng(true));
    }

    public function cos_lat() {
        return cos($this->lat(true));
    }

    public function cos_lng() {
        return cos($this->lng(true));
    }

    public function get_distance_to(lat_lng $other) {
        $x = $this->sin_lat() * $other->sin_lat() + $this->cos_lat() * $other->cos_lat() * cos($this->lng(true) - $other->lng(true));
        if (!is_nan($acos = acos($x))) {
            return ($acos * 6371);
        } else {
            return 0;
        }
    }
}

 