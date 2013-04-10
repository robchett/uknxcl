<?php
include "SUA-data.php";

$input = explode("\r\n", $input);
$bodgeFactor = 1852;
$out = "";
$classes = Array();
$colours = array(
    "A" => array("FF0000", '000000'),
    "B" => array("003333", '003333'),
    "C" => array("FF0000", '000000'),
    "CTR" => array("FF000", 'FF0000'),
    "D" => array("0000FF", '0000FF'),
    "E" => array("00FF66", '00FF66'),
    "G" => array("FFFF00", 'FFFF00'),
    "P" => array("FF0000", 'FF0000'),
    "Q" => array("FF0000", 'FF0000'),
    "R" => array("FF0000", 'FF0000'),
    "W" => array("FFFFFF", '000000'),
    "OTHER" => array("FF000", 'FF0000'),
);

class airspace {
    public $class;
    public $name;
    public $top;
    public $base;
    public $height;
    public $type;
    // 0=circle,

}

foreach ($input as $a) {
    // Check if first entry
    $matches = "";
    if (preg_match("/^CLASS=(.*)/", $a, $matches)) {
        $current->type = $matches[1];
        continue;
    }
    if (preg_match("/^TITLE=(.*)/", $a, $matches)) {
        $current->name = str_replace("'", "\'", $matches[1]);
        continue;
    }
    if (preg_match("/BASE=(.*)/", $a, $matches)) {
        if (preg_match("/SFC/", $matches[1], $matche)) {
            $current->base = 0;
        } else if (preg_match("/FL(.*),*/", $matches[1], $matche)) {
            $current->base = $matche[1] * 100;
        } else if (preg_match("/(.*)ALT.*/", $matches[1], $matche)) {
            $current->base = $matche[1];
        }
        continue;
    }
    if (preg_match("/TOPS=(.*)/", $a, $matches)) {
        //echo $matches[1];
        if (preg_match("/FL(.*).*/", $matches[1], $matche)) {
            $current->top = $matche[1] * 100;
        } else if (preg_match("/(.*)ALT.*/", $matches[1], $matche)) {
            $current->top = $matche[1];
        } else if (preg_match("/(.*)MSL.*/", $matches[1], $matche)) {
            $current->top = $matche[1];
        }
        continue;
    }
    if (preg_match("/^TYPE=(.*)/", $a, $matches)) {
        if (isset($current) && isset($current->class)) {
            $t = $current->class;
            $classes["$t"] [] = $current;
        }
        $current = new airspace();
        if ($matches[1] == "") $matches[1] = "OTHER";
        $matches[1] = str_replace("/", "", $matches[1]);
        $current->class = $matches[1];
        $current->prev_lat = 0;
        $current->prev_lon = 0;
        continue;
    }

    if (preg_match("/CIRCLE RADIUS=(.*) CENTRE=(N|S)([0-9]{6}) (E|W)([0-9]{7})/", $a, $matches)) {
        $lat = ConvertCord($matches[3], $matches[2]) * 2 * M_PI / 360;
        $lon = ConvertCord($matches[5], $matches[4]) * 2 * M_PI / 360;
        $radius = round($matches[1], 2) * $bodgeFactor;
        $t = getArcCords(array($lat, $lon, $radius), 0, 360, -1);
        $current->cords = $t[0];
        $current->cords2 = $t[0];
        continue;
    }
    if (preg_match("/POINT=([N|S])([0-9]{6}) ([W|E])([0-9]{7})/", $a, $matches)) {
        $lat = ConvertCord($matches[2], $matches[1]);
        $lon = ConvertCord($matches[4], $matches[3]);
        $current->cords .= encode($lat - $current->prev_lat) . encode($lon - $current->prev_lon);
        $current->cords2 .= "$lon,$lat,0 ";
        $current->prev_lat = $lat;
        $current->prev_lon = $lon;
        continue;
    }
    if (preg_match("/ANTI-CLOCKWISE RADIUS=(.*) CENTRE=([N|S])([0-9]{6}) ([W|E])([0-9]{7}) TO=([N|S])([0-9]{6}) ([W|E])([0-9]{7})/", $a, $matches)) {
        $lat1 = $current->prev_lat * M_PI / 180;
        $lon1 = $current->prev_lon * M_PI / 180;
        $latC = ConvertCord($matches[3], $matches[2]) * 2 * M_PI / 360;
        $lonC = ConvertCord($matches[5], $matches[4]) * 2 * M_PI / 360;
        $lat2 = ConvertCord($matches[7], $matches[6]) * 2 * M_PI / 360;
        $lon2 = ConvertCord($matches[9], $matches[8]) * 2 * M_PI / 360;
        $radius = round($matches[1], 2) * $bodgeFactor;
        $t = getArcCords(array($latC, $lonC, $radius), get_bearing(array($latC, $lonC), array($lat1, $lon1)), get_bearing(array($latC, $lonC), array($lat2, $lon2)), 1, $lat1 * 180 / M_PI, $lon1 * 180 / M_PI);
        $current->cords .= $t[0];
        $current->cords2 .= $t[1];
        $current->prev_lat = $lat2 * 180 / M_PI;
        $current->prev_lon = $lon2 * 180 / M_PI;
        continue;
    }
    if (preg_match("/^CLOCKWISE RADIUS=(.*) CENTRE=([N|S])([0-9]{6}) ([W|E])([0-9]{7}) TO=([N|S])([0-9]{6}) ([W|E])([0-9]{7})/", $a, $matches)) {
        $lat1 = $current->prev_lat * M_PI / 180;
        $lon1 = $current->prev_lon * M_PI / 180;
        $latC = ConvertCord($matches[3], $matches[2]) * 2 * M_PI / 360;
        $lonC = ConvertCord($matches[5], $matches[4]) * 2 * M_PI / 360;
        $lat2 = ConvertCord($matches[7], $matches[6]) * 2 * M_PI / 360;
        $lon2 = ConvertCord($matches[9], $matches[8]) * 2 * M_PI / 360;
        $radius = round($matches[1], 2) * $bodgeFactor;
        $t = getArcCords(array($latC, $lonC, $radius), get_bearing(array($latC, $lonC), array($lat1, $lon1)), get_bearing(array($latC, $lonC), array($lat2, $lon2)), -1, $lat1 * 180 / M_PI, $lon1 * 180 / M_PI);
        $current->cords .= $t[0];
        $current->cords2 .= $t[1];
        $current->prev_lat = $lat2 * 180 / M_PI;
        $current->prev_lon = $lon2 * 180 / M_PI;
        continue;
    }
    //  else $out.=  $a."";
}
sort($classes);
$out .= "
function airspace(airClass, flightLevel,Top, points, strokeWeight, strokeColour, strokeOpacity, fillColour, fillOpacity, name, type) 
{ 
    var polygon = new google.maps.Polygon({
        strokeColor: strokeColour,
        strokeWeight: strokeWeight,
        clickable:true, 
        strokeOpacity: strokeOpacity,
        path: google.maps.geometry.encoding.decodePath(points), 
        fillColor: fillColour,
        fillOpacity: fillOpacity,
        zIndex: (185 - flightLevel),
        title:name});
    as[airSpaces] = [polygon, airClass, flightLevel, Top,false]; 
    ++airSpaces; 
} 

maximum_base=7500;
function setHeight(val)
{ 
    maximum_base = val;
}
";
foreach ($classes as $c) {
    $out .= "
function load{$c[0]->class}(bool) { 
    c{$c[0]->class} = bool; 
    if(class{$c[0]->class}loaded || !c{$c[0]->class}) return; 
    class{$c[0]->class}loaded = true; 
";
    foreach ($c as $d) {

        $out .= "airspace('$d->class',$d->base,$d->top,'$d->cords',1,'#{$colours[$d->type][0]}',0.8,'#{$colours[$d->type][1]}',0.2,'$d->name','$d->type');
";
    }
    $out .= "}
";

}

$out .= "function LoadAll(bool){
";
foreach ($classes as $c) {
    $out .= "load{$c[0]->class}(bool);document.getElementById('airspace_{$c[0]->class}').checked = bool;
";
}
$out .= "}
";

$out .= "  var as = new Array();
    var airSpaces = 0; 
";
foreach ($classes as $c) {
    $out .= "var c{$c[0]->class} = false;class{$c[0]->class}loaded = false;
";
}

$out .= "
function reload(currentHeight){ 
    for (i in as){
        if(varyWithTrack!==undefined && (as[i][2] >= currentHeight || as[i][3] <= currentHeight)){
            as[i][0].setMap(null);
            as[i][4]=false;
            continue;
        }
        else if(as[i][2] >= maximum_base){
            as[i][0].setMap(null);
            as[i][4]=false;
            continue;
        } 
        switch(as[i][1]){ 
";
foreach ($classes as $c) {
    $out .= "case('{$c[0]->class}'): if(!c{$c[0]->class}){as[i][0].setMap(null);as[i][4]=false;}else if(!as[i][4])as[i][0].setMap(Map); as[i][4]=true;break;
";
}
$out .= "
        } 
    } 
} 
";

fwrite(fopen("../javascript/Airspace.js", 'w'), $out);

function encode($lat) {
    $lat = $lat * 1e5;
    if ($lat < 0) {
        $neg = true;
        $lat = decbin(-$lat);
        $lat = str_pad($lat, 32, "0", STR_PAD_LEFT);
        $lat = bit_invert($lat);
        $lat = binary_add_1($lat);
    } else {
        $neg = false;
        $lat = decbin($lat);
        $lat = str_pad($lat, 32, "0", STR_PAD_LEFT);
    }
    $lat = binary_left_shift($lat);
    if ($neg) {
        $lat = bit_invert($lat);
    }
    $lat = strrev($lat);
    $lat = str_split($lat, 5);
    $out = "";
    for ($i = 0; $i < 5; $i++) {
        $out = chr(bindec("1" . strrev($lat[$i])) + 63) . $out;
    }
    $out = chr(bindec("0" . strrev($lat[5])) + 63) . $out;
    return strrev($out);
}

function bit_invert($lat) {
    $lat = str_replace("0", "2", $lat);
    $lat = str_replace("1", "0", $lat);
    $lat = str_replace("2", "1", $lat);
    return $lat;
}

function binary_add_1($lat) {
    $size = strlen($lat);
    while (1 && $size > 0) {
        if ($lat[$size - 1] == "0") {
            $lat[$size - 1] = "1";
            return $lat;
        } else {
            $lat[$size - 1] = "0";
            $size--;
        }
    }
    return bit_invert($lat);
}

function binary_left_shift($lat) {
    $a = substr($lat, 1);
    return $a . "0";
}

function getArcCords($cords, $start, $end, $dir, $prev_lat = 0, $prev_lon = 0) {
    $out = "";
    $out2 = "";
    $angularDistance = $cords [2] / 6371000;
    $count = 0;
    if ($dir < 0) $totat_angle = ($end - $start);
    else {
        if ($start > $end) {
            $end += 360;
            $totat_angle = -(($start) - ($end));
        } else
            $totat_angle = -(($start) - ($end)) - 360;
    }

    for ($i = $start; $count <= 48; $i += $totat_angle / 48) {
        //$out.= "$i";
        $bearing = deg2rad($i);
        $lat = Asin(Sin($cords [0]) * Cos($angularDistance) + Cos($cords [0]) * Sin($angularDistance) * Cos($bearing));

        $dlon = Atan2(Sin($bearing) * Sin($angularDistance) * Cos($cords [0]), Cos($angularDistance) - Sin($cords [0]) * Sin($lat));

        $lon = fmod(($cords [1] + $dlon + M_PI), 2 * M_PI) - M_PI;
        $latOut = rad2deg($lat);
        $lonOut = rad2deg($lon);
        $out .= encode($latOut - $prev_lat) . encode($lonOut - $prev_lon);
        $out2 .= "$lonOut,$latOut,0 ";

        $prev_lat = $latOut;
        $prev_lon = $lonOut;
        $count++;
    }
    return array($out, $out2);
}

function ConvertCord($cord, $dir) {
    $deg = floor($cord / 10000);
    $min = floor(($cord - $deg * 10000) / 100);
    $sec = $cord - $min * 100 - $deg * 10000;
    $cord = $deg + $min * 1 / 60 + $sec / 3600;
    if ($dir == "S" || $dir == "W") $cord = -$cord;
    return $cord;
}

function get_bearing($coord1, $coord2) {
    $y = sin($coord2[1] - $coord1[1]) * cos($coord2[0]);
    $x = cos($coord1[0]) * sin($coord2[0]) - sin($coord1[0]) * cos($coord2[0]) * cos($coord2[1] - $coord1[1]);
    $brng = atan2($y, $x) * 180 / M_PI;
    return $brng;
}


$out = "<?xml version='1.0' encoding='UTF-8'?>
<Document>";
foreach ($classes as $c) {
    $out .= "
    <Style id=\"{$c[0]->type}\">
        <LineStyle>
            <width>0.8</width>
            <color>FF{$colours[$c[0]->type][0]}</color>
        </LineStyle>
        <PolyStyle>
            <color>66{$colours[$c[0]->type][1]}</color>
        </PolyStyle>
    </Style>
    <Folder>
        <name>{$c[0]->class}</name>
    ";
    foreach ($c as $d) {
        $out .= "
        <Placemark>
            <name>$d->name</name>
            <description>$d->name</description>   
            <styleUrl>#{$d->class}</styleUrl>
            <Polygon>
                <outerBoundaryIs>
                    <LinearRing>
                        <coordinates>
                            $d->cords2
                        </coordinates>
                    </LinearRing>
                </outerBoundaryIs>
            </Polygon>
        </Placemark>
";
    }
    $out .= "
    </Folder>
";
}

fwrite(fopen("../javascript/Airspace.kml", 'w'), $out . "</Document>");
?>