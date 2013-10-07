<?php
namespace track;

class triangle extends task {

    public function get_distance($dp = 10) {
        if (!isset($this->distance)) {
            if (isset($this->waypoints) && $this->waypoints->count() == 5) {
                if ($this->waypoints->spherical) {
                    $this->distance = $this->waypoints->offsetGet(1)->get_dist_to($this->waypoints[2]) + $this->waypoints->offsetGet(2)->get_dist_to($this->waypoints[3]) + $this->waypoints->offsetGet(1)->get_dist_to($this->waypoints[3]) - $this->waypoints->first()->get_dist_to($this->waypoints[4]);
                } else {
                    $this->distance = $this->waypoints->offsetGet(1)->get_dist_to_precise($this->waypoints[2]) + $this->waypoints->offsetGet(2)->get_dist_to_precise($this->waypoints[3]) + $this->waypoints->offsetGet(1)->get_dist_to_precise($this->waypoints[3]) - $this->waypoints->first()->get_dist_to_precise($this->waypoints[4]);
                }
            } else {
                $this->distance = 0;
            }
        }
        return number_format($this->distance, $dp);
    }

    public function get_coordinates() {
        $html = '';
        if (isset( $this->waypoints) && $this->waypoints->count() == 5) {
            $waypoints = $this->waypoints;
            $this->waypoints = new track_point_collection(array($waypoints[1], $waypoints[2], $waypoints[3], $waypoints[1]));
            $html .= parent::get_coordinates();
            $this->waypoints = $waypoints;
        } else {
            return parent::get_coordinates();
        }
        return $html;
    }

    protected function get_kml_table() {
        $html = '';
        if (isset( $this->waypoints) && $this->waypoints->count() == 5) {
            $waypoints = $this->waypoints;
            $this->waypoints = new track_point_collection(array($waypoints[1], $waypoints[2], $waypoints[3], $waypoints[1]));
            $html = parent::get_kml_table();
            $this->waypoints = $waypoints;
        }
        return $html;
    }

    public function get_kml_coordinates() {
        $html = '';
        if (isset( $this->waypoints) && $this->waypoints->count() == 5) {
            $waypoints = $this->waypoints;
            $this->waypoints = new track_point_collection(array($waypoints[1], $waypoints[2], $waypoints[3], $waypoints[1]));
            $html = parent::get_kml_coordinates();
            $this->waypoints = $waypoints;
        }
        return $html;
    }
}