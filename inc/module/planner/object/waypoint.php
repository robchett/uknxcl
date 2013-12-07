<?php
namespace module\planner\object;

use classes\table;
use traits\table_trait;

class waypoint extends table {

    use table_trait;

    public $lat;
    public $lon;

    /**
     * @param array $fields
     * @param array $options
     * @return waypoint_array
     */
    public static function get_all(array $fields, array $options = []) {
        $waypoint_array = new waypoint_array();
        $waypoint_array->get_all(get_called_class(), $fields, $options);
        return $waypoint_array;
    }

    /**
     * @return string
     */
    public function get_js() {
        return 'map.planner.addWaypoint(' . $this->lat . ',' . $this->lon . ');';
    }
}