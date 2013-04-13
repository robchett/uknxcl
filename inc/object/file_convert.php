<?php

class file_convert {
    static function Decimalize($a) {

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
                $a[$i - $start] = self::LatLongToOSGrid($c[1], $c[0]);
                if ($out[0] != "") $out[0] = $out[0] . ";" . $a[$i - $start];
                else $out[0] = $a[$i - $start];
                $out[1][$i - $start] = $a[$i - $start];
            }
        } else {
            for ($i = $start; $i < $start + $cnum; $i++) {
                $c = explode(",", substr($file[$i], 10), 4);
                print_r($c);
                $a[$i - $start] = self::LatLongToOSGrid($c[1], $c[0]);
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

    static function LatLongToOSGrid($p, $q) {
        $lat = self::toRad($p);
        $lon = self::toRad($q);
        $a = 6377563.396;
        $b = 6356256.910;
        $F0 = 0.9996012717;
        $lat0 = self::toRad(49);
        $lon0 = self::toRad(-2);
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

    static function padLZ($n) {
        $j = strlen($n);
        for ($i = 0; $i < 3 - $j; $i++) $n = '0' . $n;
        return $n;
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

    static function time_split_kml_plus_js(track $track, $coords = "") {
        $count1 = 0;
        $output = kml::get_kml_header();
        /** @var track_part */
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
            if ($coords != "") $output .= self::outputTask($coords);
        }
        $output .= kml::get_kml_footer();

        $outFile = fopen($track->get_file_loc() . '/Track.kml', 'w');
        fwrite($outFile, $output);
        $outFile = fopen($track->get_file_loc() . '/Track_Earth.kml', 'w');
        fwrite($outFile, $output);

        $track->generate_js();
    }

    static function outputTask($task) {
        $out = new stdClass();
        $out->in = $task;
        $out->o = "";
        $out->task_array = explode(';', $task);
        foreach ($out->task_array as &$a) {
            $a = self::OSGridToLatLong($a);
        }
        foreach ($out->task_array as $matches) {
            $out->o .= "<Placemark>
        <Polygon>
            <tessellate>1</tessellate>
            <outerBoundaryIs>
                <LinearRing>
                    <coordinates>
                    " . self::getCircleCords2($matches) . "
                    </coordinates>
                </LinearRing>
            </outerBoundaryIs>
        </Polygon>
    </Placemark>";
        }
        $out->o .= "<Placemark>
    <LineString>
    <altitudeMode>clampToGround</altitudeMode>
        <coordinates>";

        foreach ($out->task_array as $cords) {
            $lon = $cords [0];
            $lat = $cords[1];
            $out->o .= $lat . ',' . $lon . ",-100 ";
        }
        $out->o .= "</coordinates>
    </LineString>
    </Placemark>
    ";
        $out->task = $task;
        return $out->o;
    }

    static function OSGridToLatLong($gridRef) {
        $gr = self::gridrefLetToNum($gridRef);
        $E = $gr[0];
        $N = $gr[1];

        $a = 6377563.396;
        $b = 6356256.910; // Airy 1830 major & minor semi-axes
        $F0 = 0.9996012717; // NatGrid scale factor on central meridian
        $lat0 = 49 * pi() / 180;
        $lon0 = -2 * pi() / 180; // NatGrid true origin
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

        return Array(self::toDeg($lat), self::toDeg($lon));
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
        // skip grid letters to get numeric part of ref, stripping any spaces:
        $gridref = substr($gridref, 2);
        // append numeric part of references to grid index:
        $e .= substr($gridref, 0, (strlen($gridref) / 2));
        $n .= substr($gridref, (strlen($gridref) / 2));
        // normalise to 1m grid, rounding up to centre of grid square:
        $e .= '50';
        $n .= '50';
        return Array($e, $n);
    }

    static function toDeg($a) { // convert radians to degrees (signed)
        return $a * 180 / pi();
    }

    static function getCircleCords2($cords) {
        $cords[0] = self::toRad($cords[0]);
        $cords[1] = self::toRad($cords[1]);
        $out = "";
        $angularDistance = 400 / 6378137;
        for ($i = 0; $i <= 360; $i++) {
            $bearing = deg2rad($i);
            $lat = Asin(Sin($cords [0]) * Cos($angularDistance) + Cos($cords [0]) * Sin($angularDistance) * Cos($bearing));

            $dlon = Atan2(Sin($bearing) * Sin($angularDistance) * Cos($cords [0]), Cos($angularDistance) - Sin($cords [0]) * Sin($lat));

            $lon = fmod(($cords [1] + $dlon + M_PI), 2 * M_PI) - M_PI;
            $latOut = rad2deg($lat);
            $lonOut = rad2deg($lon);
            $out .= "$lonOut,$latOut,0 ";
        }
        return $out;
    }

    static function toRad($a) { // convert degrees to radians
        return $a * pi() / 180;
    }
}