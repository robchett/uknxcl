<?php
class dimension extends table { use table_trait

    public $table_key = 'did';
    public static $module_id = 11;

    public static function get_all(array $fields, array $options = array()) {
        return dimension_array::get_all($fields, $options);
    }
}

class dimension_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'dimension_iterator');
        $this->iterator = new dimension_iterator($input);
    }

    /* @return dimension */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class dimension_iterator extends table_iterator {

    /* @return dimension */
    public function key() {
        return parent::key();
    }
}