<?php
class launch_type extends table { use table_trait;

    const WINCH = 3;
    const AERO = 2;
    const FOOT = 1;

    public $table_key = 'lid';
    public static $module_id = 9;
    public $lid;
    public $title;
    public $fn;

    public static function get_all(array $fields, array $options = array()) {
        return launch_type_array::get_all($fields, $options);
    }
}

class launch_type_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'launch_type_iterator');
        $this->iterator = new launch_type_iterator($input);
    }

    /* @return launch_type */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class launch_type_iterator extends table_iterator {

    /* @return launch_type */
    public function key() {
        return parent::key();
    }
}