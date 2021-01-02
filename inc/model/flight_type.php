<?php

namespace model;

use classes\table;


class flight_type extends table {


    const OD_ID = 1;
    const OR_ID = 2;
    const GO_ID = 3;
    const TR_ID = 4;
    const FT_ID = 5;
    protected static $all_rows;
    public $multi;
    public $multi_defined;
    public $ftid;
    public $fn;
    public string $title;

    /**
     * @param $type
     * @param int $season
     * @param bool $defined
     * @return int
     */
    public static function get_multiplier($type, int $season = 2001, bool $defined = false): int {
        if (!isset(self::$all_rows)) {
            self::$all_rows = flight_type::get_all([]);
        }
        if ($defined && $type == static::OD_ID) {
            $type = static::GO_ID;
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
     * @return string
     */
    public static function get_type($type): string {
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
