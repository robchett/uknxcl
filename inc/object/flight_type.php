<?php
class flight_type { use table;

    const OD_ID = 1;
    const OR_ID = 2;
    const GO_ID = 3;
    const TR_ID = 4;
    const FT_ID = 5;

    public $table_key = 'ftid';
    public static $module_id = 10;
    public $ftid;
    public $fn;
    public $title;

    protected static $all_rows;

    /* @return flight_type_array */
    public static function get_all(array $fields, array $options = array()) {
        return flight_type_array::get_all($fields, $options);
    }

    public static function get_multiplier($type, $season, $defined = false) {
        if(!isset(self::$all_rows)) {
            self::$all_rows = flight_type::get_all(array());
        }
        foreach (self::$all_rows as $ftype) {
            if($ftype->ftid == $type) {
                if($season < 2001) {
                    return $defined ? $ftype->multi_defined_old : $ftype->multi_old ;
                } else {
                    return $defined ? $ftype->multi_defined : $ftype->multi;
                }
            }
        }
    }

    public static function get_title($type) {
        if(!isset(self::$all_rows)) {
            self::$all_rows = flight_type::get_all(array());
        }
        foreach (self::$all_rows as $ftype) {
            if($ftype->ftid == $type) {
                return $ftype->title;
            }
        }
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