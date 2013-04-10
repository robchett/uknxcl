<?php

class get {

    static function ent($html) {
        return htmlentities(html_entity_decode($html));
    }

    public static function fn($str) {
        return str_replace(array(' ','.',',','-'),'_',strtolower($str));
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

    static function  type($int) {
        switch ($int) {
            case (1) :
                return 'OD';
            case (2) :
                return 'OR';
            case (3) :
                return 'GO';
            case (4) :
                return 'TR';
            case (5) :
                return 'OD';
        }
    }

    static function launch_letter($int) {
        switch ($int) {
            case (1) :
                return "";
            case (2) :
                return "A ";
            case (3) :
                return "W";
        }
    }

    static function launch($int) {
        switch ($int) {
            case (1) :
                return "Foot";
            case (2) :
                return "Aerotow";
            case (3) :
                return "Winch";
        }
    }

    static function flight_type($a) {
        switch ($a) {
            case (1) :
                return "Open Distance";
            case (2) :
                return "Out and Return";
            case (3) :
                return "Goal";
            case (4) :
                return "Triangle";
            case 5 :
                return "Failed Triangle";
        }
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
    }

}
