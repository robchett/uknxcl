<?php
class comp_group extends table {
    public $table_key = 'cgid';
    public static $module_id = 18;

    /* @return comp_group_array */
    public static function get_all(array $fields, array $options = array()) {
        return comp_group_array::get_all($fields, $options);
    }
}

class comp_group_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'comp_group_iterator');
        $this->iterator = new comp_group_iterator($input);
    }

    /* @return comp_group */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class comp_group_iterator extends table_iterator {

    /* @return comp_group */
    public function key() {
        return parent::key();
    }
}