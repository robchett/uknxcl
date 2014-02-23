<?php

namespace module\planner\object;

use classes\table_array;

/**
 * Class waypoint_array
 * @package planner
 */
class waypoint_array extends table_array {

    /**
     * @return string
     */
    public function get_js() {
        $js = '';
        $this->reset_iterator();
        foreach ($this as $waypoint) {
            /** @var waypoint $waypoint */
            $js .= $waypoint->get_js();
        }
        return $js;
    }
}