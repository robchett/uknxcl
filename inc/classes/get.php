<?php

namespace classes;

use classes\get as _get;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use model\flight_type;
use model\launch_type;

class get {

    public static array $cms_settings = [];
    protected static $type_array;
    protected static $flight_type;
    protected static array $launch_letter = [
        launch_type::FOOT  => '',
        launch_type::AERO  => 'Aero',
        launch_type::WINCH => 'Winch',
    ];
    protected static $launch_title;

    #[Pure]
    static function ent($html): string {
        return htmlentities(html_entity_decode($html));
    }

    #[Pure]
    public static function __class_name($object): bool|string {
        if (is_string($object)) {
            $name = trim($object, '\\');
        } else {
            $name = trim(get_class($object), '\\');
        }
        if (($pos = strrpos($name, '\\')) !== false) {
            $pos++;
        }
        return substr($name, $pos);
    }

    public static function recursive_glob($root, $pattern, $flags = 0): array {
        $files = [];
        $directories = glob('/' . trim($root, '/') . '/*', GLOB_ONLYDIR);
        if ($directories) {
            foreach ($directories as $dir) {
                $files = array_merge($files, self::recursive_glob($dir, $pattern, $flags));
            }
        }
        $root_files = glob('/' . trim($root, '/') . '/' . $pattern);
        if ($root_files) {
            $files = array_merge($files, $root_files);
        }
        return $files;
    }

    public static function setting($setting, $default = '') {
        if (!self::$cms_settings) {
            $res = db::select('_cms_setting')
                ->add_field_to_retrieve('key')
                ->add_field_to_retrieve('value')
                ->execute();
            while ($obj = $res->fetchObject()) {
                self::$cms_settings[$obj->key] = $obj->value;
            }
        }
        return isset(self::$cms_settings[$setting]) ? self::$cms_settings[$setting] : $default;
    }

    public static function __namespace($object, $index = null) {
        $name = trim(get_class($object), '\\');
        if (isset($index)) {
            return array_reverse(explode('\\', substr($name, 0, strrpos($name, '\\'))))[$index];
        } else {
            return substr($name, 0, strrpos($name, '\\'));
        }
    }

    public static function unique_fn($table, $field, $str): string {
        $base_fn = _get::fn($str);
        if (db::select($table)->add_field_to_retrieve($field)->filter($field . '=:fn', ['fn' => $base_fn])->execute()->rowCount()) {
            $cnt = 0;
            do {
                $fn = $base_fn . '_' . ++$cnt;
            } while (db::select($table)->add_field_to_retrieve($field)->filter($field . '=:fn', ['fn' => $fn])->execute()->rowCount());
            return $fn;
        } else {
            return $base_fn;
        }

    }

    public static function fn($str): string {
        return trim(str_replace([' ', '.', ',', '-', '?', '#'], '_', strtolower($str)), '_');
    }

    static function trim_root($string): array|string {
        return str_replace(root, '', $string);
    }

    #[Pure]
    static function ordinal($num): string {
        if (!in_array(($num % 100), [11, 12, 13])) {
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1:
                    return $num . 'st';
                case 2:
                    return $num . 'nd';
                case 3:
                    return $num . 'rd';
            }
        }
        return $num . 'th';
    }

    #[NoReturn]
    public static function header_redirect($url = '', $code = 404) {
        header('Location:' . (!strstr('http', $url) ? 'http://' . host . '/' . trim($url, '/') : $url), $code);
        die();
    }

    static function bool($a): string {
        if ($a)
            return "Yes";
        else
            return "No";
    }

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

    static function launch_letter($int): mixed {
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

    static function launch($int) {
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

    static function colour($i): string {
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

    static function kml_colour($i): string {
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

    static function js_colour($i): string {
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
