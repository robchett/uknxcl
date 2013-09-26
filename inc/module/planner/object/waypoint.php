<?php
namespace planner;
class waypoint extends \table {
    use \table_trait;

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

/**
 * Class waypoint_array
 * @package planner
 */
class waypoint_array extends \table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, '\planner\waypoint_iterator');
        $this->iterator = new waypoint_iterator($input);
    }

    /* @return waypoint */
    public function next() {
        return parent::next();
    }

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

/**
 * Class waypoint_iterator
 * @package planner
 */
class waypoint_iterator extends \table_iterator {

    /* @return waypoint */
    public function key() {
        return parent::key();
    }
}