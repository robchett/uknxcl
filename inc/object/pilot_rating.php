<?php
class pilot_rating extends table {
    use table_trait;

    public $table_key = 'prid';
    public static $module_id = 15;

    /**
     * @param array $fields
     * @param array $options
     * @return pilot_rating_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return pilot_rating_array::get_all($fields, $options);
    }
}

/**
 * Class pilot_rating_array
 */
class pilot_rating_array extends table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, 'pilot_rating_iterator');
        $this->iterator = new pilot_rating_iterator($input);
    }

    /* @return pilot_rating */
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
 * Class pilot_rating_iterator
 */
class pilot_rating_iterator extends table_iterator {

    /* @return pilot_rating */
    public function key() {
        return parent::key();
    }
}