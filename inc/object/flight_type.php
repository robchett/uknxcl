<?php
/**
 * Class flight_type
 */
class flight_type extends table { use table_trait;

    const OD_ID = 1;
    const OR_ID = 2;
    const GO_ID = 3;
    const TR_ID = 4;
    const FT_ID = 5;
    public $multi;
    public $multi_defined;

    public $table_key = 'ftid';
    public static $module_id = 10;
    public $ftid;
    public $fn;
    public $title;

    protected static $all_rows;

    /**
     * @param array $fields
     * @param array $options
     * @return table_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return flight_type_array::get_all($fields, $options);
    }

    /**
     * @param $type
     * @param $season
     * @param bool $defined
     * @return mixed
     */
    public static function get_multiplier($type, $season, $defined = false) {
        if (!isset(self::$all_rows)) {
            self::$all_rows = flight_type::get_all(array());
        }
        foreach (self::$all_rows as $flight_type) {
            if ($flight_type->ftid == $type) {
                if ($season < 2001) {
                    return $defined ? $flight_type->multi_defined_old : $flight_type->multi_old;
                } else {
                    return $defined ? $flight_type->multi_defined : $flight_type->multi;
                }
            }
        }
        return 1;
    }

    /**
     * @param $type
     * @return mixed
     */
    public static function get_title($type) {
        if (!isset(self::$all_rows)) {
            self::$all_rows = flight_type::get_all(array());
        }
        foreach (self::$all_rows as $flight_type) {
            if ($flight_type->ftid == $type) {
                return $flight_type->title;
            }
        }
        return '';
    }

}

/**
 * Class flight_type_array
 */
class flight_type_array extends table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, 'flight_type_iterator');
        $this->iterator = new flight_type_iterator($input);
    }

    /* @return flight_type */
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
 * Class flight_type_iterator
 */
class flight_type_iterator extends table_iterator {

    /* @return flight_type */
    public function key() {
        return parent::key();
    }
}