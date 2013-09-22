<?php
class gender extends table { use table_trait;

    public $table_key = 'gid';
    public static $module_id = 14;

    /**
     * @param array $fields
     * @param array $options
     * @return gender_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return gender_array::get_all($fields, $options);
    }
}

/**
 * Class gender_array
 */
class gender_array extends table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, 'gender_iterator');
        $this->iterator = new gender_iterator($input);
    }

    /* @return gender */
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
 * Class gender_iterator
 */
class gender_iterator extends table_iterator {

    /* @return gender */
    public function key() {
        return parent::key();
    }
}