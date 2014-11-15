<?php

namespace classes;

use track\task;
use track\track;

class geometry {

    const EARTH_RADIUS = 6371000;

    public static function coordinate_normalise($coordinate) {
        $parts = explode(' ', $coordinate, 2);
        $dir = 1;
        if(isset($parts[0][0]) && !is_numeric($parts[0][0])) {
            $char = strtolower($parts[0][0]);
            if($char == 's' || $char == 'w' || $char == '-') {
                $dir = -1;
            }
        } else {
            $dir = ($parts[0] >= 0 ? 1 :  -1);
        }
        if (count($parts) >= 2) {
            return $parts[0] + $dir * (5 / 300 * $parts[1]);
        }
        return $coordinate;
    }

    public static function get_distance_ellipsoid(lat_lng $obj1, lat_lng $obj2) {
        $a = 6378.137 / 1.852;
        $f = 1 / 298.257223563;
        $EPS = 0.00000000005;
        $iter = 1;
        $MAXITER = 100;
        $arOut = [0, 0.0, M_PI];
        if (abs($obj1->lat(true) - $obj2->lat(true)) < $EPS && (abs($obj1->lng(true) - $obj2->lng(true)) < $EPS || abs(abs($obj1->lng(true) - $obj2->lng(true)) - 2 * M_PI) < $EPS)) {
            return $arOut;
        }
        $r = 1 - $f;
        $tu1 = $r * tan($obj1->lat(true));
        $tu2 = $r * tan($obj2->lat(true));
        $cu1 = 1 / (sqrt(1 + $tu1 * $tu1));
        $su1 = $cu1 * $tu1;
        $cu2 = 1 / (sqrt(1 + $tu2 * $tu2));
        $s1 = $cu1 * $cu2;
        $b1 = $s1 * $tu2;
        $f1 = $b1 * $tu1;
        $x = $obj2->lng(true) - $obj1->lng(true);
        do {
            $iter++;
            $sx = sin($x);
            $cx = cos($x);
            $tu1 = $cu2 * $sx;
            $tu2 = $b1 - $su1 * $cu2 * $cx;
            $sy = sqrt($tu1 * $tu1 + $tu2 * $tu2);
            $cy = $s1 * $cx + $f1;
            $y = atan2($sy, $cy);
            $sa = $s1 * $sx / $sy;
            $c2a = 1 - $sa * $sa;
            $cz = $f1 + $f1;
            if ($c2a > 0) {
                $cz = $cy - $cz / $c2a;
            }
            $e = $cz * $cz * 2 - 1;
            $c = ((-3 * $c2a + 4) * $f + 4) * $c2a * $f / 16;
            $d = $x;
            $x = (($e * $cy * $c + $cz) * $sy * $c + $y) * $sa;
            $x = (1 - $c) * $x * $f + $obj2->lng(true) - $obj1->lng(true);
        } while ((abs($d - $x) > $EPS) && ($iter < $MAXITER));
        $x = sqrt((1 / ($r * $r) - 1) * $c2a + 1);
        $x++;
        $x = ($x - 2) / $x;
        $c = 1 - $x;
        $c = ($x * $x / 4 + 1) / $c;
        $d = (0.375 * $x * $x - 1) * $x;
        $x = $e * $cy;
        $s = (((($sy * $sy * 4 - 3) * (1 - $e - $e) * $cz * $d / 6 - $x) * $d / 4 + $cz) * $sy * $d + $y) * $c * $a * $r;
        $arOut = $s * 1.852;
        return $arOut;
    }

    static function lat_long_to_os(lat_lng $point, $precision = 6) {
        $point = gps_datums::convert($point, 'WGS84', 'OSGB36');
        $lat = $point->lat(true);
        $lon = $point->lng(true);
        $a = 6377563.396;
        $b = 6356256.910;
        $F0 = 0.9996012717;
        $lat0 = deg2rad(49);
        $lon0 = deg2rad(-2);
        $N0 = -100000;
        $E0 = 400000;
        $e2 = 1 - (($b * $b) / ($a * $a));
        $n = ($a - $b) / ($a + $b);
        $n2 = $n * $n;
        $n3 = $n * $n * $n;

        $cosLat = cos($lat);
        $sinLat = sin($lat);
        $nu = $a * $F0 / sqrt(1 - $e2 * $sinLat * $sinLat); // transverse radius of curvature
        $rho = $a * $F0 * (1 - $e2) / pow(1 - $e2 * $sinLat * $sinLat, 1.5); // meridional radius of curvature
        $eta2 = $nu / $rho - 1;

        $Ma = (1 + $n + (5 / 4) * $n2 + (5 / 4) * $n3) * ($lat - $lat0);
        $Mb = (3 * $n + 3 * $n * $n + (21 / 8) * $n3) * sin($lat - $lat0) * cos($lat + $lat0);
        $Mc = ((15 / 8) * $n2 + (15 / 8) * $n3) * sin(2 * ($lat - $lat0)) * cos(2 * ($lat + $lat0));
        $Md = (35 / 24) * $n3 * sin(3 * ($lat - $lat0)) * cos(3 * ($lat + $lat0));
        $M = $b * $F0 * ($Ma - $Mb + $Mc - $Md); // meridional arc

        $cos3lat = $cosLat * $cosLat * $cosLat;
        $cos5lat = $cos3lat * $cosLat * $cosLat;
        $tan2lat = tan($lat) * tan($lat);
        $tan4lat = $tan2lat * $tan2lat;

        $I = $M + $N0;
        $II = ($nu / 2) * $sinLat * $cosLat;
        $III = ($nu / 24) * $sinLat * $cos3lat * (5 - $tan2lat + 9 * $eta2);
        $IIIA = ($nu / 720) * $sinLat * $cos5lat * (61 - 58 * $tan2lat + $tan4lat);
        $IV = $nu * $cosLat;
        $V = ($nu / 6) * $cos3lat * ($nu / $rho - $tan2lat);
        $VI = ($nu / 120) * $cos5lat * (5 - 18 * $tan2lat + $tan4lat + 14 * $eta2 - 58 * $tan2lat * $eta2);

        $dLon = $lon - $lon0;
        $dLon2 = $dLon * $dLon;
        $dLon3 = $dLon2 * $dLon;
        $dLon4 = $dLon3 * $dLon;
        $dLon5 = $dLon4 * $dLon;
        $dLon6 = $dLon5 * $dLon;

        $N = $I + $II * $dLon2 + $III * $dLon4 + $IIIA * $dLon6;
        $E = $E0 + $IV * $dLon + $V * $dLon3 + $VI * $dLon5;
        return self::gridref_number_to_letter($E, $N, $precision);
    }

    /**
     * @param string $gridRef
     * @return lat_lng
     */
    static function os_to_lat_long($gridRef) {
        $gr = self::gridref_letter_to_number($gridRef);
        $E = $gr[0];
        $N = $gr[1];

        $a = 6377563.396;
        $b = 6356256.910; // Airy 1830 major & minor semi-axes
        $F0 = 0.9996012717; // NatGrid scale factor on central meridian
        $lat0 = 49 * M_PI / 180;
        $lon0 = -2 * M_PI / 180; // NatGrid true origin
        $N0 = -100000;
        $E0 = 400000; // northing & easting of true origin; metres
        $e2 = 1 - ($b * $b) / ($a * $a); // eccentricity squared

        $n = ($a - $b) / ($a + $b);
        $n2 = $n * $n;
        $n3 = $n * $n * $n;

        $lat = $lat0;
        $M = 0;
        do {
            $lat = ($N - $N0 - $M) / ($a * $F0) + $lat;

            $Ma = (1 + $n + (5 / 4) * $n2 + (5 / 4) * $n3) * ($lat - $lat0);
            $Mb = (3 * $n + 3 * $n * $n + (21 / 8) * $n3) * sin($lat - $lat0) * cos($lat + $lat0);
            $Mc = ((15 / 8) * $n2 + (15 / 8) * $n3) * sin(2 * ($lat - $lat0)) * cos(2 * ($lat + $lat0));
            $Md = (35 / 24) * $n3 * sin(3 * ($lat - $lat0)) * cos(3 * ($lat + $lat0));
            $M = $b * $F0 * ($Ma - $Mb + $Mc - $Md); // meridional arc

        } while ($N - $N0 - $M >= 0.00001); // ie until < 0.01mm

        $cosLat = cos($lat);
        $sinLat = sin($lat);
        $nu = $a * $F0 / sqrt(1 - $e2 * $sinLat * $sinLat); // transverse radius of curvature
        $rho = $a * $F0 * (1 - $e2) / pow(1 - $e2 * $sinLat * $sinLat, 1.5); // meridional radius of curvature
        $eta2 = $nu / $rho - 1;

        $tanLat = tan($lat);
        $tan2lat = $tanLat * $tanLat;
        $tan4lat = $tan2lat * $tan2lat;
        $tan6lat = $tan4lat * $tan2lat;
        $secLat = 1 / $cosLat;
        $nu3 = $nu * $nu * $nu;
        $nu5 = $nu3 * $nu * $nu;
        $nu7 = $nu5 * $nu * $nu;
        $VII = $tanLat / (2 * $rho * $nu);
        $VIII = $tanLat / (24 * $rho * $nu3) * (5 + 3 * $tan2lat + $eta2 - 9 * $tan2lat * $eta2);
        $IX = $tanLat / (720 * $rho * $nu5) * (61 + 90 * $tan2lat + 45 * $tan4lat);
        $X = $secLat / $nu;
        $XI = $secLat / (6 * $nu3) * ($nu / $rho + 2 * $tan2lat);
        $XII = $secLat / (120 * $nu5) * (5 + 28 * $tan2lat + 24 * $tan4lat);
        $XIIA = $secLat / (5040 * $nu7) * (61 + 662 * $tan2lat + 1320 * $tan4lat + 720 * $tan6lat);

        $dE = ($E - $E0);
        $dE2 = $dE * $dE;
        $dE3 = $dE2 * $dE;
        $dE4 = $dE2 * $dE2;
        $dE5 = $dE3 * $dE2;
        $dE6 = $dE4 * $dE2;
        $dE7 = $dE5 * $dE2;
        $lat = $lat - $VII * $dE2 + $VIII * $dE4 - $IX * $dE6;
        $lon = $lon0 + $X * $dE - $XI * $dE3 + $XII * $dE5 - $XIIA * $dE7;

        return gps_datums::convert(new lat_lng(rad2deg($lat), rad2deg($lon)), 'OSGB36', 'WGS84');
    }

    static function get_circle_coordinates(lat_lng $center_coordinate, $radius = 400) {
        $out = "";
        $angularDistance = $radius / 6378137;
        for ($i = 0; $i <= 360; $i++) {
            $bearing = deg2rad($i);
            $lat = asin($center_coordinate->sin_lat() * cos($angularDistance) + $center_coordinate->cos_lat() * sin($angularDistance) * cos($bearing));
            $dlon = atan2(sin($bearing) * sin($angularDistance) * $center_coordinate->cos_lat(), cos($angularDistance) - $center_coordinate->sin_lat() * sin($lat));
            $lon = fmod(($center_coordinate->lng(true) + $dlon + M_PI), 2 * M_PI) - M_PI;
            $out .= rad2deg($lon) . ',' . rad2deg($lat) . ',0 ';
        }
        return $out;
    }

    static function gridref_letter_to_number($gridref) {
        // get numeric values of letter references, mapping A->0, B->1, C->2, etc:
        $l1 = ord($gridref[0]) - ord('A');
        $l2 = ord($gridref[1]) - ord('A');
        // shuffle down letters after 'I' since 'I' is not used in grid:
        if ($l1 > 7) $l1--;
        if ($l2 > 7) $l2--;
        // convert grid letters into 100km-square indexes from false origin (grid square SV):
        $e = (($l1 - 2) % 5) * 5 + ($l2 % 5);
        $n = (19 - floor($l1 / 5) * 5) - floor($l2 / 5);
        // skip grid letters to get numeric part of ref, stripping any space's'=>
        $gridref = substr($gridref, 2);
        $precision = (strlen($gridref) / 2);
        // append numeric part of references to grid index:
        $e .= substr($gridref, 0, $precision);
        $n .= substr($gridref, $precision);
        // normalise to 1m grid, rounding up to centre of grid square:
        $factor = 5 * pow(10, 4 - $precision);
        $e .= $factor;
        $n .= $factor;

        return [$e, $n];
    }

    static function gridref_number_to_letter($e, $n, $digits) {
        $precision = $digits / 2;
        // get the 100km-grid indices
        $e100k = floor($e / 100000);
        $n100k = floor($n / 100000);

        if ($e100k < 0 || $e100k > 8 || $n100k < 0 || $n100k > 12) { //echo "broke";
        }

        // translate those into numeric equivalents of the grid letters
        $l1 = (19 - $n100k) - (19 - $n100k) % 5 + floor(($e100k + 10) / 5);
        $l2 = (19 - $n100k) * 5 % 25 + $e100k % 5;

        // compensate for skipped 'I' and build grid letter-pairs
        if ($l1 > 7) $l1++;
        if ($l2 > 7) $l2++;
        $letPair = chr($l1 + ord('A')) . chr($l2 + ord('A'));

        // strip 100km-grid indices from easting & northing, and reduce precision
        $e = floor(($e % 100000) / pow(10, 5 - $precision));
        $n = floor(($n % 100000) / pow(10, 5 - $precision));
        $gridRef = $letPair . self::pad_number($e, $precision) . self::pad_number($n, $precision);
        return $gridRef;
    }

    static function osgb32_to_wgs84($lat, $lng) {

    }

    static function get_task_output(task $task) {
        $xml = '<Folder><name>Task</name>';
        foreach ($task->waypoints as $point) {
            $xml .= "<Placemark>
        <Style>
            <PolyStyle>
              <color>99ffffaa</color>
              <fill>1</fill>
              <outline>1</outline>
            </PolyStyle>
        </Style>
        <Polygon>
            <tessellate>1</tessellate>
            <outerBoundaryIs>
                <LinearRing>
                    <coordinates>
                    " . self::get_circle_coordinates($point) . "
                    </coordinates>
                </LinearRing>
            </outerBoundaryIs>
        </Polygon>
    </Placemark>";
        }
        $xml .= "<Placemark>
    <LineStyle>
      <color>FFFFFF00</color>
      <width>2</width>
    </LineStyle>
    <LineString>
    <altitudeMode>clampToGround</altitudeMode>
        <coordinates>";

        /** @var lat_lng $point */
        foreach ($task->waypoints as $point) {
            $xml .= $point->lng() . ',' . $point->lat() . ",-100 ";
        }
        $xml .= "</coordinates>
    </LineString>
    </Placemark></Folder>
    ";
        return $xml;
    }

    static function pad_number($number, $precision) {
        $length = strlen($number);
        for ($i = 0; $i < $precision - $length; $i++) {
            $number = '0' . $number;
        }
        return $number;
    }
}