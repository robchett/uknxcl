<?php
class gender { use table;
    public $table_key = 'gid';
    public static $module_id = 14;

    /* @return gender_array */
    public static function get_all(array $fields, array $options = array()) {
        return gender_array::get_all($fields, $options);
    }
}

class gender_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'gender_iterator');
        $this->iterator = new gender_iterator($input);
    }

    /* @return gender */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class gender_iterator extends table_iterator {

    /* @return gender */
    public function key() {
        return parent::key();
    }
}