<?php
include '../phpsqlajax_dbinfo.php';
include '../FlightFiles/file_convert.php';
if (isset($_POST['submit'])) {
    $type = $_POST['type'];
    $file = file($_FILES["file"]["tmpilot.name"]);
    $line = 1;
    echo '<table><tr><th style="width:50px">ID</th><th style="width:120px">Description</th><th style="width:80px">Lat</th><th style="width:80px">Lon</th><th style="width:80px">Height</th></tr>';
    while (($t = $file[$line]) != false) {
        (preg_match('/(.{6})\s+(.{13})\s+(.{14})\s+(\d+)\s{2,3}(.*)/', $t, $matches));
        echo '<tr><td>' . $matches[1] . '</td><td>' . strtolower($matches[5]) . '</td><td>';
        switch ($type) {
            case(0):
                echo deg_min_2_dec($matches[2]) . '</td><td>' . deg_min_2_dec($matches[3]) . '</td><td>';
                break;
            case(1):
                echo deg_min_2_dec_min($matches[2]) . '</td><td>' . deg_min_2_dec_min($matches[3]) . '</td><td>';
                break;
            case(2):
                echo deg_min_2_dec_min_sec($matches[2]) . '</td><td>' . deg_min_2_dec_min_sec($matches[3]) . '</td><td>';
                break;
            case(3):
                echo deg_min_2_os($matches[2], $matches[3]) . '</td><td>';
                break;
        }
        echo $matches[4] . '</td></tr>';
        $line++;
    }
    echo '</table>';
} else {
    echo '
	<form name="input" id="input" action="" method="post" enctype="multipart/form-data">
	<input type="file" onchange="CheckKML(this)" name="file" id="file" class="file" style="width:300px"/>
        <input type="radio" name="type" value=0 checked="checked"/>Decimal Degree<input type="radio" name="type" value=1/>Degree Seconds (dd ss)<input type="radio" name="type" value=2/>dd mm ss<input type="radio" name="type" value=3/>os grid ref
	<input type="submit"  id="submit" name="submit"/>
</form>';
}

function dec2hms($cord) {
    return $cord = ((int) substr($cord, 0, -1)) / 100;
}

function deg_min_2_dec($in) {
    $in = explode(' ', $in);
    if ($in[0] == "S" || $in[0] == "W")
        return sprintf("%02.4f", (-(round((int) $in[1] + ((int) $in[2]) / 60 + ((float) $in[3]) / 3600, 4))));
    else
        return sprintf("%02.4f", ((round((int) $in[1] + ((int) $in[2]) / 60 + ((float) $in[3]) / 3600, 4))));
}

function deg_min_2_dec_min($in) {
    $in = explode(' ', $in);
    return $in[0] . ' ' . $in[1] . ' ' . round(((int) $in[2] + ((float) $in[3]) / 60), 4);
}

function deg_min_2_dec_min_sec($in) {
    $in = explode(' ', $in);
    return $in[0] . ' ' . $in[1] . ' ' . $in[2] . ' ' . $in[3];

}

function deg_min_2_os($lat, $lon) {
    $lat = explode(' ', $lat);
    if ($lat[0] == "S" || $lat[0] == "W")
        $lat = -round((int) $lat[1] + ((int) $lat[2]) / 60 + ((float) $lat[3]) / 3600, 4);
    else
        $lat = round((int) $lat[1] + ((int) $lat[2]) / 60 + ((float) $lat[3]) / 3600, 4);
    $lon = explode(' ', $lon);
    if ($lon[0] == "S" || $lon[0] == "W")
        $lon = -round((int) $lon[1] + ((int) $lon[2]) / 60 + ((float) $lon[3]) / 3600, 4);
    else
        $lon = round((int) $lon[1] + ((int) $lon[2]) / 60 + ((float) $lon[3]) / 3600, 4);

    return geometry::lat_long_to_os(new lat_lng($lat, $lon));
}

