<?php

class get {

    private static $settings;

    static function ent($html) {
        return htmlentities(html_entity_decode($html));
    }

    public static function __class_name($object) {
        if(is_string($object)) {
            $name = trim($object, '\\');
        } else {
            $name = trim(get_class($object),'\\');
        }
        if(($pos = strrpos($name, '\\')) !== false) {
            $pos++;
        }
        return substr($name, $pos);
    }

    public static function __namespace($object) {
        $name = trim(get_class($object), '\\');
        return substr($name, 0, strrpos($name, '\\'));
    }

    public static function fn($str) {
        return str_replace(array(' ', '.', ',', '-'), '_', strtolower($str));
    }

    static function trim_root($string) {
        return str_replace(root, '', $string);
    }

    static function ordinal($num) {
        if (!in_array(($num % 100), array(11, 12, 13))) {
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

    public static function header_redirect($url = '', $code = 404) {
        header('Location:' . (!strstr('http', $url) ? 'http://' . host . '/' . trim($url, '/') : $url), $code);
        die();
    }

    protected static $type_array;

    static function type($int) {
        if (!isset(self::$type_array)) {
            $types = flight_type::get_all(array('ftid', 'fn'));
            /** @var flight_type $type */
            foreach ($types as $type) {
                self::$type_array[$type->ftid] = $type->fn;
            }
        }
        if (isset(self::$type_array[$int])) {
            return self::$type_array[$int];
        }
        return false;
    }

    protected static $flight_type;

    static function flight_type($int) {
        if (!isset(self::$flight_type)) {
            $types = flight_type::get_all(array('ftid', 'title'));
            /** @var flight_type $type */
            foreach ($types as $type) {
                self::$flight_type[$type->ftid] = $type->title;
            }
        }
        if (isset(self::$flight_type[$int])) {
            return self::$flight_type[$int];
        }
        return false;
    }

    protected static $launch_letter;

    static function  launch_letter($int) {
        if (!isset(self::$launch_letter)) {
            $types = launch_type::get_all(array('lid', 'fn'));
            /** @var launch_type $type */
            foreach ($types as $type) {
                self::$launch_letter[$type->lid] = $type->fn;
            }
        }
        if (isset(self::$launch_letter[$int])) {
            return self::$launch_letter[$int];
        }
        return false;
    }

    protected static $launch_title;

    static function  launch($int) {
        if (!isset(self::$launch_title)) {
            $types = launch_type::get_all(array('lid', 'title'));
            /** @var launch_type $type */
            foreach ($types as $type) {
                self::$launch_title[$type->lid] = $type->title;
            }
        }
        if (isset(self::$launch_title[$int])) {
            return self::$launch_title[$int];
        }
        return false;
    }

    static function bool($a) {
        if ($a)
            return "Yes";
        else
            return "No";
    }

    static function colour($i) {
        $colour = array(
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
            "0000FF");
        return $colour[$i];
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

    public static function ini($key, $block = 'site', $default = null) {
        if (!isset(self::$settings)) {
            if (is_readable(root . '/.conf/config.ini')) {
                self::$settings = parse_ini_file(root . '/.conf/config.ini', true);
            } else {
                throw new Exception('Could not find ini file.');
            }
            if (defined('host') && is_readable(root . '/.conf/' . host . '.ini')) {
                $sub_settings = parse_ini_file(root . '/.conf/' . host . '.ini', true);
                foreach ($sub_settings as $ini_block => $ini_keys) {
                    if (isset(self::$settings[$ini_block])) {
                        self::$settings[$ini_block] = $ini_keys;
                    } else {
                        self::$settings[$ini_block] = array_merge(self::$settings[$ini_block], $ini_keys);
                    }
                }
            }
        }


        if (isset(self::$settings[$block][$key])) {
            return self::$settings[$block][$key];
        } else if (isset($default)) {
            return $default;
        } else {
            throw new Exception('Setting not found and no default provided');
        }

    }
}
