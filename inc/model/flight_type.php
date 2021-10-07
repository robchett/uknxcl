<?php

namespace model;

use classes\table;
use classes\table_array;
use classes\tableOptions;
use classes\interfaces\model_interface;

class flight_type  implements model_interface {
    use table;


    const OD_ID = 1;
    const OR_ID = 2;
    const GO_ID = 3;
    const TR_ID = 4;
    const FT_ID = 5;
    /**
     * @var ?table_array<self>
     */
    protected static ?table_array $all_rows;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $ftid,
        public string $title,
        public string $fn,
        public float $multi,
        public float $multi_defined,
        public float $multi_old,
        public float $multi_defined_old,
    )
    {
    }

    /**
     * @param int $type
     * @param int $season
     * @param bool $defined
     * @return float
     */
    public static function get_multiplier(int $type, int $season = 2001, bool $defined = false): float {
        self::$all_rows ??= flight_type::get_all(new tableOptions());
        if ($defined && $type == static::OD_ID) {
            $type = self::GO_ID;
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

    public static function get_type(int $type): string {
        self::$all_rows ??= flight_type::get_all(new tableOptions());
        foreach (self::$all_rows as $flight_type) {
            if ($flight_type->ftid == $type) {
                return $flight_type->title;
            }
        }
        return '';
    }

}
