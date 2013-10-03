<?php
namespace module\planner\object;

use classes\table;
use traits\table_trait;

class waypoint extends table {

    use table_trait;

    public $lat;
    public $lon;
    public $table_key = 'wid';
    public static $module_id = 22;

    /**
     * @param array $fields
     * @param array $options
     * @return waypoint_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return waypoint_array::get_all($fields, $options);
    }

    /**
     * @return string
     */
    public function get_js() {
        return 'map.planner.addWaypoint(' . $this->lat . ',' . $this->lon . ');';
    }
}