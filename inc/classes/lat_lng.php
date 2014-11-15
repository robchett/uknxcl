<?php

namespace classes;

class lat_lng extends \coordinate {

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
}

 