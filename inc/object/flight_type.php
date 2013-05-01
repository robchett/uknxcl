<?php
class flight_type extends table {
    public $table_key = 'ftid';
    public static $module_id = 10;
    public $ftid;
    public $fn;
    public $title;

    /* @return flight_type_array */
    public static function get_all(array $fields, array $options = array()) {
        return flight_type_array::get_all($fields, $options);
    }
}

class flight_type_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'flight_type_iterator');
        $this->iterator = new flight_type_iterator($input);
    }

    /* @return flight_type */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class flight_type_iterator extends table_iterator {

    /* @return flight_type */
    public function key() {
        return parent::key();
    }
}