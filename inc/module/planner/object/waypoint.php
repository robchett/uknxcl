<?php
namespace planner;
class waypoint extends \table {
    use \table_trait;

    /** @var float */
    public $lat;
    /** @var float */
    public $lon;
    public $table_key = 'wid';
    public static $module_id = 22;

    public static function get_all(array $fields, array $options = array()) {
        return waypoint_array::get_all($fields, $options);
    }

    public function get_js() {
        return 'map.planner.addWaypoint(' . $this->lat . ',' . $this->lon . ');';
    }
}

class waypoint_array extends \table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'waypoint_iterator');
        $this->iterator = new waypoint_iterator($input);
    }

    /* @return waypoint */
    public function next() {
        return parent::next();
    }

    public static function get_all(array $fields_to_retrieve, $options = array(), $log_sql = 0) {
        return parent::get_all($fields_to_retrieve, $options, $log_sql);
    }

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

class waypoint_iterator extends \table_iterator {

    /* @return waypoint */
    public function key() {
        return parent::key();
    }
}