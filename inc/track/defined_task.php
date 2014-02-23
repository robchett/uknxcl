<?php
namespace track;

use classes\geometry;
use object\flight_type;

class defined_task extends task {

    public function get_task() {

        $xml = '';
        if ($this->ftid != flight_type::TR_ID) {
            $xml .= geometry::get_task_output($this);
        } else {
            $waypoints = $this->waypoints;
            $this->waypoints = new track_point_collection([$waypoints[1], $waypoints[2], $waypoints[3], $waypoints[1]]);
            $xml .= geometry::get_task_output($this);
            $this->waypoints = $waypoints;
        }
        return $xml;
    }
}