<?php
namespace track;

class out_and_return extends task {

    public function get_distance($dp = 10) {
        if (!isset($this->distance)) {
            if (isset($this->waypoints)) {
                if ($this->waypoints->spherical) {
                    $this->distance = $this->waypoints->first()->get_dist_to($this->waypoints[1]) * 2 - $this->waypoints->first()->get_dist_to($this->waypoints[2]);
                } else {
                    $this->distance = $this->waypoints->first()->get_dist_to_precise($this->waypoints[1]) * 2 - $this->waypoints->first()->get_dist_to_precise($this->waypoints[2]);
                }
            } else {
                $this->distance = 0;
            }
        }
        return number_format($this->distance, $dp);
    }
}