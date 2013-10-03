<?php
namespace classes;

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
        $class = new \stdClass();
        $class->lat = ($this->south + $this->north) / 2;
        $class->lon = $this->crosses_antimeridian() ? $this->normalize_lon($this->west + $this->get_lon_center($this->west, $this->east) / 2) : ($this->west + $this->east) / 2;
        return $class;
    }

    public function get_js() {
        $class = new \stdClass();
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
        $ne = new lat_lng($this->north, $this->east);
        $sw = new lat_lng($this->south, $this->west);
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
 