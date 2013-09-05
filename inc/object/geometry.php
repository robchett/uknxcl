<?php

class geometry {

    const EARTH_RADIUS = 6371000;

    static function Decimalize($a) {

    }

    public static function get_distance(track_point $obj1, track_point $obj2) {
        $x = $obj1->sin_lat * $obj2->sin_lat + $obj1->cos_lat * $obj2->cos_lat * cos($obj1->lng_rad() - $obj2->lng_rad());
        if (!is_nan($acos = acos($x))) {
            return ($acos * 6371);
        } else {
            return 0;
        }
    }

    public static function get_distance_ellipsoid(track_point $obj1, track_point $obj2) {
        $a = 6378.137 / 1.852;
        $f = 1 / 298.257223563;
        $EPS = 0.00000000005;
        $iter = 1;
        $MAXITER = 100;
        $arOut = array(0, 0.0, M_PI);
        if (abs($obj1->lat_rad() - $obj2->lat_rad()) < $EPS && (abs($obj1->lng_rad() - $obj2->lng_rad()) < $EPS || abs(abs($obj1->lng_rad() - $obj2->lng_rad()) - 2 * M_PI) < $EPS)) {
            return $arOut;
        }
        $r = 1 - $f;
        $tu1 = $r * tan($obj1->lat_rad());
        $tu2 = $r * tan($obj2->lat_rad());
        $cu1 = 1 / (sqrt(1 + $tu1 * $tu1));
        $su1 = $cu1 * $tu1;
        $cu2 = 1 / (sqrt(1 + $tu2 * $tu2));
        $s1 = $cu1 * $cu2;
        $b1 = $s1 * $tu2;
        $f1 = $b1 * $tu1;
        $x = $obj2->lng_rad() - $obj1->lng_rad();
        $d = $x + 1;
        while ((abs($d - $x) > $EPS) && ($iter < $MAXITER)) {
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
            $x = (1 - $c) * $x * $f + $obj2->lng_rad() - $obj1->lng_rad();
        }
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

    static function lat_long_to_os(lat_lng $point) {
        $point = gps_datums::convert($point, 'WGS84', 'OSGB36');
        $lat = $point->lat_rad();
        $lon = $point->lng_rad();
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
        return self::gridrefNumToLet($E, $N, 6);
    }

    /**
     * @param string $gridRef
     * @return lat_lng */
    static function os_to_lat_long($gridRef) {
        $gr = self::gridrefLetToNum($gridRef);
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

        return new lat_lng(rad2deg($lat), rad2deg($lon));
    }

    static function getCircleCords2(lat_lng $center_coordinate, $radius = 400) {
        $out = "";
        $angularDistance = $radius / 6378137;
        for ($i = 0; $i <= 360; $i++) {
            $bearing = deg2rad($i);
            $lat = asin($center_coordinate->sin_lat * cos($angularDistance) + $center_coordinate->cos_lat * sin($angularDistance) * cos($bearing));
            $dlon = atan2(sin($bearing) * sin($angularDistance) * $center_coordinate->cos_lat, cos($angularDistance) - $center_coordinate->sin_lat * sin($lat));
            $lon = fmod(($center_coordinate->lng_rad() + $dlon + M_PI), 2 * M_PI) - M_PI;
            $out .= rad2deg($lon) . ',' . rad2deg($lat) . ',0 ';
        }
        return $out;
    }

    static function getCords($file, $cnum, $lookup, $isOR = 0) {
        $start = 0;
        $match_no = 0;
        for ($i = 0; $i < sizeof($file); $i++) {
            if (preg_match("%<coordinates>%", $file[$i])) {
                $match_no++;
                if ($match_no == $lookup) {
                    $start = $i + 1;
                }
            }
        }
        $out[0] = "";
        $a = Array();
        if (!$isOR) {
            for ($i = $start; $i < $start + $cnum; $i++) {
                $c = explode(",", substr($file[$i], 10), 4);
                $a[$i - $start] = self::lat_long_to_os($c[1], $c[0]);
                if ($out[0] != "") $out[0] = $out[0] . ";" . $a[$i - $start];
                else $out[0] = $a[$i - $start];
                $out[1][$i - $start] = $a[$i - $start];
            }
        } else {
            for ($i = $start; $i < $start + $cnum; $i++) {
                $c = explode(",", substr($file[$i], 10), 4);
                print_r($c);
                $a[$i - $start] = self::lat_long_to_os($c[1], $c[0]);
                if ($out[0] != "") $out[0] = $out[0] . ";" . $a[$i - $start];
                else $out[0] = $a[$i - $start];
                $out[1][$i - $start] = $a[$i - $start];
            }
            if (self::getDist(Array($out[1][0], $out[1][1])) <= 0.8) {
                $copy = $out[1][1];
                $out[1][1] = $out[1][2];
                $out[1][2] = $copy;
                $c = explode(";", $out[0]);
                $out[0] = "$c[0];$c[2];$c[1]";
            }
        }
        //print_r($out[1]);
        $out[1] = self::getDist($out[1]);
        return $out;
    }

    static function getDist($cords) {
        $score = 0;
        for ($i = 0; $i < count($cords) - 1; $i++) {
            $p1 = self::gridrefLetToNum($cords[$i]);
            $p2 = self::gridrefLetToNum($cords[$i + 1]);

            $deltaE = $p2[0] - $p1[0];
            $deltaN = $p2[1] - $p1[1];

            $dist = sqrt($deltaE * $deltaE + $deltaN * $deltaN);

            $score += ($dist / 1000);
        }
        $score = round($score, 2);
        return $score;
    }

    static function gridrefLetToNum($gridref) {
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
        // append numeric part of references to grid index:
        $e .= substr($gridref, 0, (strlen($gridref) / 2));
        $n .= substr($gridref, (strlen($gridref) / 2));
        // normalise to 1m grid, rounding up to centre of grid square:
        $e .= '50';
        $n .= '50';
        return Array($e, $n);
    }

    static function gridrefNumToLet($e, $n, $digits) {
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
        $e = floor(($e % 100000) / pow(10, 5 - $digits / 2));
        $n = floor(($n % 100000) / pow(10, 5 - $digits / 2));
        $gridRef = $letPair . self::padLZ($e) . self::padLZ($n);
        return $gridRef;
    }

    static function osgb32_to_wgs84($lat, $lng) {

    }

    static function outputTask(task $task) {
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
                    " . self::getCircleCords2($point) . "
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

    static function padLZ($n) {
        $j = strlen($n);
        for ($i = 0; $i < 3 - $j; $i++) $n = '0' . $n;
        return $n;
    }

    static function time_split_kml_plus_js(track $track) {
        $count1 = 0;
        $output = kml::get_kml_header();
        /** @track_part */
        foreach ($track->track_parts as $a) {
            $output .= '
    <Placemark>
      <name>Flight</name>
      <Style>
        <LineStyle>
          <color>FF' . get::colour(++$count1) . '</color>
          <width>2</width>
        </LineStyle>
      </Style>
      ' . $track->get_time_meta_data($a->start_point, $a->end_point) . '
      ' . $track->get_kml_linestring() . '
    </Placemark>';
        }
        if (isset($track->task)) {
            $output .= self::outputTask($track->task);
        }
        $output .= kml::get_kml_footer();

        $outFile = fopen($track->get_file_loc() . '/Track.kml', 'w');
        fwrite($outFile, $output);
        $outFile = fopen($track->get_file_loc() . '/Track_Earth.kml', 'w');
        fwrite($outFile, $output);

        $track->generate_js();
    }

}

class gps_datums {
    private static $ellipse = [
        'WGS84' => ['a' => 6378137, 'b' => 6356752.3142, 'f' => 0.0033528106647475],
        'GRS80' => ['a' => 6378137, 'b' => 6356752.314140, 'f' => 0.0033528106811823],
        'OSGB36' => ['a' => 6377563.396, 'b' => 6356256.910, 'f' => 0.0033408506414971],
        'AiryModified' => ['a' => 6377340.189, 'b' => 6356034.448, 'f' => 0.003340850692839],
        'Intl1924' => ['a' => 6378388.000, 'b' => 6356911.946, 'f' => 0.0033670033670034]
    ];

    private static $transform = [
        'OSGB36' => ['tx' => -446.448, 'ty' => 125.157, 'tz' => -542.060, 'rx' => -0.1502, 'ry' => -0.2470, 'rz' => -0.8421, 's' => 20.4894],
        'ED50' => ['tx' => 89.5, 'ty' => 93.8, 'tz' => 123.1, 'rx' => 0.0, 'ry' => 0.0, 'rz' => 0.156, 's' => -1.2],
        'Irl1975' => ['tx' => -482.530, 'ty' => 130.596, 'tz' => -564.557, 'rx' => -1.042, 'ry' => -0.214, 'rz' => -0.631, 's' => -8.150]];

    public static function convert(lat_lng $point, $from, $to) {
        $from_to = true; // Moving in the positive direction;
        if (!isset(self::$ellipse[$from]) && !isset(self::$ellipse[$to])) {
            throw new Exception('The transform for this conversion doesn\'t exitst. Please add a Helmert transform');
        }
        if (!isset(self::$ellipse[$to]) || !isset(self::$ellipse[$from])) {
            throw new Exception('No ellipsis is available for this transform');
        }

        if (!isset(self::$transform[$to])) {
            $transform = [];
            $from_to = false;
            foreach (self::$transform[$from] as $key => $val) {
                $transform[$key] = -$val;
            }
        } else {
            $transform = self::$transform[$to];
        }
        $e1 = ($from_to ? self::$ellipse[$from] : self::$ellipse[$to]);
        $e2 = ($from_to ? self::$ellipse[$to] : self::$ellipse[$from]);

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
        $H = 24.7; // for the moment

        $eSq = ($a * $a - $b * $b) / ($a * $a);
        $nu = $a / sqrt(1 - $eSq * $sinPhi * $sinPhi);

        $x1 = ($nu + $H) * $cosPhi * $cosLambda;
        $y1 = ($nu + $H) * $cosPhi * $sinLambda;
        $z1 = ((1 - $eSq) * $nu + $H) * $sinPhi;


        // -- 2: apply helmert transform using appropriate params

        $tx = $transform['tx'];
        $ty = $transform['ty'];
        $tz = $transform['tz'];
        $rx = deg2rad($transform['rx'] / 3600); // normalise seconds to radians
        $ry = deg2rad($transform['ry'] / 3600);
        $rz = deg2rad($transform['rz'] / 3600);
        $s1 = ($transform['s'] / 1e6) + 1; // normalise ppm to (s+1)

        // apply transform
        $x2 = $tx + $x1 * $s1 - $y1 * $rz + $z1 * $ry;
        $y2 = $ty + $x1 * $rz + $y1 * $s1 - $z1 * $rx;
        $z2 = $tz - $x1 * $ry + $y1 * $rx + $z1 * $s1;


        // -- 3: convert cartesian to polar coordinates (using ellipse 2)

        $a = $target_ellipse['a'];
        $b = $target_ellipse['b'];
        $precision = 4 / $a; // results accurate to around 4 metres

        $eSq = ($a * $a - $b * $b) / ($a * $a);
        $p = sqrt($x2 * $x2 + $y2 * $y2);
        $phi = atan2($z2, $p * (1 - $eSq));
        $phiP = 2 * M_PI;
        while (abs($phi - $phiP) > $precision) {
            $nu = $a / sqrt(1 - $eSq * sin($phi) * sin($phi));
            $phiP = $phi;
            $phi = atan2($z2 + $eSq * $nu * sin($phi), $p);
        }
        $lambda = atan2($y2, $x2);
        $H = $p / cos($phi) - $nu;

        return new lat_lng(rad2deg($phi), rad2deg($lambda), $H);
    }
}
