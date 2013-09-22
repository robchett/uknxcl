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

    /**
     * @param array $fields
     * @param array $options
     * @return launch_type_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return launch_type_array::get_all($fields, $options);
    }
}

/**
 * Class launch_type_array
 */
class launch_type_array extends table_array {


    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, 'launch_type_iterator');
        $this->iterator = new launch_type_iterator($input);
    }

    /* @return launch_type */
    public function next() {
        return parent::next();
    }

    /**
     *
     */
    protected function set_statics() {
        parent::set_statics();
    }
}

/**
 * Class launch_type_iterator
 */
class launch_type_iterator extends table_iterator {

    /* @return launch_type */
    public function key() {
        return parent::key();
    }
}