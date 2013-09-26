<?php
namespace cms;
class field_type extends \table {
    use \table_trait;

    public $table_key = 'ftid';
    public static $module_id = 16;

    /**
     * @param array $fields
     * @param array $options
     * @return field_type_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return field_type_array::get_all($fields, $options);
    }
}

/**
 * Class field_type_array
 * @package cms
 */
class field_type_array extends \table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, '\cms\field_type_iterator');
        $this->iterator = new field_type_iterator($input);
    }

    /* @return field_type */
    public function next() {
        return parent::next();
    }
}

/**
 * Class field_type_iterator
 * @package cms
 */
class field_type_iterator extends \table_iterator {

    /* @return field_type */
    public function key() {
        return parent::key();
    }
}