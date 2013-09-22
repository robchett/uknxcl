<?php
namespace comps;
class comp_group extends \table {
    use \table_trait;

    public $table_key = 'cgid';
    public static $module_id = 18;

    /**
     * @param array $fields
     * @param array $options
     * @return \table_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return comp_group_array::get_all($fields, $options);
    }
}

/**
 * Class comp_group_array
 * @package comps
 */
class comp_group_array extends \table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, 'comp_group_iterator');
        $this->iterator = new comp_group_iterator($input);
    }

    /* @return comp_group */
    public function next() {
        return parent::next();
    }
}

/**
 * Class comp_group_iterator
 * @package comps
 */
class comp_group_iterator extends \table_iterator {

    /* @return comp_group */
    public function key() {
        return parent::key();
    }
}