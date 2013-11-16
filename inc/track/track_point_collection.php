<?php
namespace track;

use classes\collection;

class track_point_collection extends collection {

    /**
     * @return track_point
     */
    public $spherical = true;

    /** @return track_point */
    public function first() {
        return parent::first();
    }

    /**
     * @param array $indexes
     * @return string
     */
    public function get_coordinates(array $indexes) {
        $coordinates = array();
        foreach ($indexes as $index) {
            $coordinates[] = $this->offsetGet($index)->get_coordinate();
        }
        return implode(';', $coordinates);
    }

    public function get_distance() {
        $distance = 0;
        if ($this->count() && $this->count() > 2) {
            foreach (range(0, $this->count() - 2) as $index) {
                if ($this->spherical) {
                    $distance += $this->offsetGet($index)->get_dist_to($this[$index + 1]);
                } else {
                    $distance += $this->offsetGet($index)->get_dist_to_precise($this[$index + 1]);
                }
            }
        }
        return $distance;
    }

    public function get_kml_coordinates() {
        $coordinates = array();
        if ($this->count()) {
            foreach (range(0, $this->count() - 1) as $index) {
                $coordinates[] = $this->offsetGet($index)->get_kml_coordinate();
            }
        }
        return $coordinates;
    }

    public function get_session_coordinates($indexes) {
        $coordinates = array();
        foreach ($indexes as $index) {
            $coordinates[] = $this->offsetGet($index)->get_coordinate() . ':' . $this->ele;
        }
        return implode(';', $coordinates);
    }

    public function unshift($int = 1) {
        parent::unshift($int);
    }

    /**
     * @return track_point
     */
    public function last() {
        return parent::last();
    }
}
 