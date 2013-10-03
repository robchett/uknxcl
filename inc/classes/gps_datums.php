<?php

namespace classes;

class gps_datums {

    private static $ellipse = [
        'WGS84' => ['a' => 6378137, 'b' => 6356752.3142],
        'GRS80' => ['a' => 6378137, 'b' => 6356752.314140],
        'OSGB36' => ['a' => 6377563.396, 'b' => 6356256.909],
        'AiryModified' => ['a' => 6377340.189, 'b' => 6356034.448],
        'Intl1924' => ['a' => 6378388.000, 'b' => 6356911.946]
    ];

    private static $transform = [
        'OSGB36' => ['tx' => -446.448, 'ty' => 125.157, 'tz' => -542.060, 'rx' => -0.1502, 'ry' => -0.2470, 'rz' => -0.8421, 's' => 20.4894],
        'ED50' => ['tx' => 89.5, 'ty' => 93.8, 'tz' => 123.1, 'rx' => 0.0, 'ry' => 0.0, 'rz' => 0.156, 's' => -1.2],
        'Irl1975' => ['tx' => -482.530, 'ty' => 130.596, 'tz' => -564.557, 'rx' => -1.042, 'ry' => -0.214, 'rz' => -0.631, 's' => -8.150]];

    public static function convert(lat_lng $point, $from, $to) {
        if (!isset(self::$ellipse[$from]) && !isset(self::$ellipse[$to])) {
            throw new \Exception('The transform for this conversion doesn\'t exitst. Please add a Helmert transform');
        }
        if (!isset(self::$ellipse[$to]) || !isset(self::$ellipse[$from])) {
            throw new \Exception('No ellipsis is available for this transform');
        }

        if (!isset(self::$transform[$to])) {
            $transform = [];
            foreach (self::$transform[$from] as $key => $val) {
                $transform[$key] = -$val;
            }
        } else {
            $transform = self::$transform[$to];
        }
        $e1 = self::$ellipse[$from];
        $e2 = self::$ellipse[$to];

        return self::__convert($point, $e1, $e2, $transform);
    }

    private static function __convert(lat_lng $point, $source_ellipse, $target_ellipse, $transform) {
        $lat = $point->lat_rad();
        $lon = $point->lng_rad();

        $a = $source_ellipse['a'];
        $b = $source_ellipse['b'];

        $sinPhi = sin($lat);
        $cosPhi = cos($lat);
        $sinLambda = sin($lon);
        $cosLambda = cos($lon);
        $H = 0; // for the moment

        $eSq = ($a * $a - $b * $b) / ($a * $a);
        $nu = $a / sqrt(1 - $eSq * $sinPhi * $sinPhi);

        $x1 = ($nu + $H) * $cosPhi * $cosLambda;
        $y1 = ($nu + $H) * $cosPhi * $sinLambda;
        $z1 = ((1 - $eSq) * $nu + $H) * $sinPhi;


        $tx = $transform['tx'];
        $ty = $transform['ty'];
        $tz = $transform['tz'];
        $rx = deg2rad($transform['rx'] / 3600);
        $ry = deg2rad($transform['ry'] / 3600);
        $rz = deg2rad($transform['rz'] / 3600);
        $s1 = ($transform['s'] / 1000000) + 1; // normalise ppm to (s+1)

        $x2 = $tx + ($x1 * $s1) - ($y1 * $rz) + ($z1 * $ry);
        $y2 = $ty + ($x1 * $rz) + ($y1 * $s1) - ($z1 * $rx);
        $z2 = $tz - ($x1 * $ry) + ($y1 * $rx) + ($z1 * $s1);


        $a = $target_ellipse['a'];
        $b = $target_ellipse['b'];
        $precision = 1 / 3600000000000;

        $eSq = ($a * $a - $b * $b) / ($a * $a);
        $p = sqrt($x2 * $x2 + $y2 * $y2);
        $phi = atan2($z2, $p * (1 - $eSq));
        $phiP = 2 * M_PI;
        $iterations = 0;
        while (abs($phi - $phiP) > $precision && $iterations < 10000000) {
            $nu = $a / sqrt(1 - $eSq * sin($phi) * sin($phi));
            $phiP = $phi;
            $phi = atan2($z2 + $eSq * $nu * sin($phi), $p);
            $iterations++;
        }
        $lambda = atan2($y2, $x2);
        $H = $p / cos($phi) - $nu;

        return new lat_lng(rad2deg($phi), rad2deg($lambda), $H);
    }
}
