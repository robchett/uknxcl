<?php

namespace classes;

use classes\get as _get;
use JetBrains\PhpStorm\NoReturn;
use model\flight_type;
use model\launch_type;

class get {

    /** @var ?array<int, string> */
    protected static ?array $flight_type;
    /** @var ?array<int, string> */
    protected static ?array $type_array;
    /** @var array<int, string>  */
    protected static array $launch_letter = [
        launch_type::FOOT  => '',
        launch_type::AERO  => 'Aero',
        launch_type::WINCH => 'Winch',
    ];
    /** @var ?array<int, string> */
    protected static ?array $launch_title = [
        launch_type::FOOT  => 'Foot',
        launch_type::AERO  => 'Aero',
        launch_type::WINCH => 'Winch',
    ];

    static function ent(string $html): string {
        return htmlentities(html_entity_decode($html));
    }

    /** @param class-string|object $object */
    public static function __class_name(string|object $object): string {
        $name = is_string($object) ? $object : get_class($object);
        return trim($name, '\\');
    }

    public static function recursive_glob(string $root, string $pattern, int $flags = 0): array {
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

    public static function __namespace(object $object, ?int $index = null): string {
        $name = trim(get_class($object), '\\');
        if (($offset = strrpos($name, '\\')) === false) {
            return '';
        }
        if (isset($index)) {
            return array_reverse(explode('\\', substr($name, 0, $offset)))[$index];
        } else {
            return substr($name, 0, $offset);
        }
    }

    public static function __basename(string|object $object): string {
        $name = is_string($object) ? $object : get_class($object);
        $parts = explode('\\', $name);
        return array_pop($parts);
    }

    public static function unique_fn(string $table, string $field, string $str): string {
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

    public static function fn(string $str): string {
        return trim(str_replace([' ', '.', ',', '-', '?', '#'], '_', strtolower($str)), '_');
    }

    static function trim_root(string $string): string {
        return str_replace(root, '', $string);
    }

    static function ordinal(int $num): string {
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


    /**
     * @noreturn
     */
    public static function header_redirect(string $url = '', int $code = 404): void {
        header('Location:' . (!str_starts_with($url, 'http') ? 'https://' . host . '/' . trim($url, '/') : $url), true, $code);
        die();
    }

    static function bool(bool $a): string {
        if ($a)
            return "Yes";
        else
            return "No";
    }

    static function type(int $int): string|false {
        if (!isset(self::$type_array)) {
            $types = flight_type::get_all(new tableOptions());
            $types->iterate(function (flight_type $type) {
                self::$type_array[$type->ftid] = $type->fn;
            }
            );
        }
        return self::$type_array[$int] ?? false;
    }

    static function flight_type(int $int): string|false {
        if (!isset(self::$flight_type)) {
            $types = flight_type::get_all(new tableOptions());
            $types->iterate(function (flight_type $type) {
                self::$flight_type[$type->ftid] = $type->title;
            }
            );
        }
        return self::$flight_type[$int] ?? false;

    }

    static function launch_letter(int $int): string|false {
        return self::$launch_letter[$int] ?? false;
    }

    static function launch(int $int): string|false {
        return self::$launch_title[$int] ?? false;
    }

    static function colour(int $i): string {
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

    static function kml_colour(int $i): string {
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

    static function js_colour(int $i): string {
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
