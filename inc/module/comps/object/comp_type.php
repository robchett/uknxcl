<?php
namespace comps;
class comp_type extends \table {
    use \table_trait;

    public $table_key = 'ctid';
    public static $module_id = '23';

    /**
     * @param array $fields
     * @param array $options
     * @return comp_type_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return comp_type_array::get_all($fields, $options);
    }
}

/**
 * Class comp_type_array
 * @package comps
 */
class comp_type_array extends \table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, 'comp_type_iterator');
        $this->iterator = new comp_type_iterator($input);
    }

    /* @return comp_type */
    public function next() {
        return parent::next();
    }
}

/**
 * Class comp_type_iterator
 * @package comps
 */
class comp_type_iterator extends \table_iterator {

    /* @return comp_type */
    public function key() {
        return parent::key();
    }
}