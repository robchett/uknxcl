<?php
namespace cms;
class field_type extends \table { use \table_trait

    public $table_key = 'ftid';
    public static $module_id = 16;

    public static function get_all(array $fields, array $options = array()) {
        return field_type_array::get_all($fields, $options);
    }
}

class field_type_array extends \table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'field_type_iterator');
        $this->iterator = new field_type_iterator($input);
    }

    /* @return field_type */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class field_type_iterator extends \table_iterator {

    /* @return field_type */
    public function key() {
        return parent::key();
    }
}