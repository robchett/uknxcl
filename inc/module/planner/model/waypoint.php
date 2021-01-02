<?php

namespace module\planner\model;

use classes\table;


class waypoint extends table {


    public string $name;
    public $lon;
    public string $title;
    private $lat;

    /**
     * @param array $fields
     * @param array $options
     * @return waypoint_array
     */
    public static function get_all(array $fields, array $options = []): waypoint_array {
        $waypoint_array = new waypoint_array();
        $waypoint_array->get_all(get_called_class(), $fields, $options);
        return $waypoint_array;
    }

    /**
     * @return string
     */
    public function get_js(): string {
        return 'map.planner.add_marker(' . $this->lat . ',' . $this->lon . ');';
    }
}