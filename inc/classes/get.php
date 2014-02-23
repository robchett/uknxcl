<?php

namespace classes;

use object\flight_type;
use object\launch_type;

class get extends \core\classes\get {

    protected static $type_array;

    static function type($int) {
        if (!isset(self::$type_array)) {
            $types = flight_type::get_all(['ftid', 'fn']);
            $types->iterate(function (flight_type $type) {
                    self::$type_array[$type->ftid] = $type->fn;
                }
            );
        }
        if (isset(self::$type_array[$int])) {
            return self::$type_array[$int];
        }
        return false;
    }

    protected static $flight_type;

    static function flight_type($int) {
        if (!isset(self::$flight_type)) {
            $types = flight_type::get_all(['ftid', 'title']);
            $types->iterate(function (flight_type $type) {
                    self::$flight_type[$type->ftid] = $type->title;
                }
            );
        }
        if (isset(self::$flight_type[$int])) {
            return self::$flight_type[$int];
        }
        return false;
    }

    protected static $launch_letter;

    static function  launch_letter($int) {
        if (!isset(self::$launch_letter)) {
            $types = launch_type::get_all(['lid', 'fn']);
            $types->iterate(function (launch_type $type) {
                    self::$launch_letter[$type->lid] = $type->fn;
                }
            );
        }
        if (isset(self::$launch_letter[$int])) {
            return self::$launch_letter[$int];
        }
        return false;
    }

    protected static $launch_title;

    static function  launch($int) {
        if (!self::$launch_title) {
            $types = launch_type::get_all(['lid', 'title']);
            $types->iterate(function (launch_type $type) {
                    self::$launch_title[$type->lid] = $type->title;
                }
            );
        }
        if (isset(self::$launch_title[$int])) {
            return self::$launch_title[$int];
        }
        return false;
    }

    static function colour($i) {
        $colour = [
            "FF0000",
            "EF000F",
            "DF001F",
            "CF002F",
            "BF003F",
            "AF004F",
            "9F005F",
            "8F006F",
            "7F007F",
            "6F008F",
            "5F009F",
            "4F00AF",
            "3F00BF",
            "2F00CF",
            "1F00DF",
            "0F00EF",
            "0000FF"];
        return $colour[$i % 16];
    }

    static function kml_colour($i) {
        switch ($i % 9) {
            case (1) :
                return 'FF0000'; // Blue
            case (2) :
                return '008000'; // Dark Green
            case (3) :
                return '00FF00'; // Green
            case (4) :
                return '008CFF'; // Orange
            case (5) :
                return '13458B'; // Brown
            case (6) :
                return 'B48246'; // Light Blue
            case (7) :
                return '9314FF'; // Pink
            case (8) :
                return '800080'; // Purple
            case (0) :
                return '0000FF'; // Red
        }
        return '';
    }

    static function js_colour($i) {
        switch ($i % 9) {
            case (1) :
                return '0000FF'; // Blue
            case (2) :
                return '008000'; // Dark Green
            case (3) :
                return '00FF00'; // Green
            case (4) :
                return 'FF8C00'; // Orange
            case (5) :
                return '8B4513'; // Brown
            case (6) :
                return '4682B4'; // Light Blue
            case (7) :
                return 'FF1493'; // Pink
            case (8) :
                return '800080'; // Purple
            case (0) :
                return 'FF0000'; // Red
        }
        return '';
    }
}