<?php

namespace object;

use classes\table;
use traits\table_trait;

class flight_type extends table {

    use table_trait;

    const OD_ID = 1;
    const OR_ID = 2;
    const GO_ID = 3;
    const TR_ID = 4;
    const FT_ID = 5;
    public $multi;
    public $multi_defined;

    public $ftid;
    public $fn;
    public $title;

    protected static $all_rows;

    /**
     * @param $type
     * @param $season
     * @param bool $defined
     * @return mixed
     */
    public static function get_multiplier($type, $season, $defined = false) {
        if (!isset(self::$all_rows)) {
            self::$all_rows = flight_type::get_all([]);
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
    public static function get_type($type) {
        if (!isset(self::$all_rows)) {
            self::$all_rows = flight_type::get_all([]);
        }
        foreach (self::$all_rows as $flight_type) {
            if ($flight_type->ftid == $type) {
                return $flight_type->title;
            }
        }
        return '';
    }

}
