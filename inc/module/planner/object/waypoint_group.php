<?php
namespace planner;
class waypoint_group extends \table {
    use \table_trait;

    public $table_key = 'wgid';
    public static $module_id = 21;

    /**
     * @param array $fields
     * @param array $options
     * @return waypoint_group_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return waypoint_group_array::get_all($fields, $options);
    }
}

/**
 * Class waypoint_group_array
 * @package planner
 */
class waypoint_group_array extends \table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, '\planner\waypoint_group_iterator');
        $this->iterator = new waypoint_group_iterator($input);
    }

    /* @return waypoint_group */
    public function next() {
        return parent::next();
    }

    /**
     *
     */
    protected function set_statics() {
        parent::set_statics();
    }
}

/**
 * Class waypoint_group_iterator
 * @package planner
 */
class waypoint_group_iterator extends \table_iterator {

    /* @return waypoint_group */
    public function key() {
        return parent::key();
    }
}