<?php
namespace cms;
class _cms_fields extends \table {
    public $fid;
    public $field_name;
    public $table_key = '';
    public static $module_id = '';

    public static $fields = [

    ];

    /**
     * @param array $fields
     * @param array $options
     * @return _cms_fields_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return _cms_fields_array::get_all($fields, $options);
    }
}

class _cms_fields_array extends \table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'cms\_cms_fields_iterator');
        $this->iterator = new _cms_fields_iterator($input);
    }

    /**
     * @param array $fields
     * @param array $options
     * @return _cms_fields[]
     */
    public static function get_all(array $fields, $options = array()) {
        return parent::get_all($fields, $options);
    }

    /* @return _cms_fields */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class _cms_fields_iterator extends \table_iterator {

    /* @return _cms_fields */
    public function key() {
        return parent::key();
    }
}