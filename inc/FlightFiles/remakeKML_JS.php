<?php
@ob_end_clean();
echo str_repeat(' ', 1024 * 64);
include 'file_convert.php';
include 'IGC_parse.php';
include '../phpsqlajax_dbinfo.php';

$result = execute("SELECT fid,Cords,Defined,Type,Base_Score FROM flight WHERE dim>1 AND Defined=1 ORDER BY fid ASC");
while (($t = db::fetch($result)) == true) {
    echo "<h4>Starting Flight : $t[fid]</h4>";
    flush();
    $track = parse_IGC($t['fid'], 0, 1);

    $times = array();
    $madeTPs = true;
    $turnpoints = explode(';', $t ['Cords']);
    if ($t['Type'] == 3) {
        unset($turnpoints[3]);
    }
    $point = 0;
    foreach ($turnpoints as $tp) {
        $coord1 = OSGridToLatLong($tp);
        for ($point; $point <= $track->ntrack_points; $point++) {
            if ($point == $track->ntrack_points) {
                $madeTPs = false;
                break 2;
            } else if ((( int ) (acos(sin($coord1 [0] * M_PI / 180) * $track->track_points [$point]->sin_lat + cos($coord1 [0] * M_PI / 180) * $track->track_points [$point]->cos_lat * cos(($coord1 [1] * M_PI / 180) - $track->track_points [$point]->lonRad)) * 6371000)) < 400) {
                $times[] = $track->track_points[$point]->time;
                continue 2;
            }
        }
    }
    if ($madeTPs) {
        $speed = ($t['Base_Score'] - sizeof($turnpoints) * .4) * 3600 / ($times[sizeof($times) - 1] - $times[0]);
        execute("UPDATE flight SET Speed=$speed WHERE fid=$t[fid]");
        echo "<p>made turnpoints $speed km/h</p>";
    } else echo"<p>Did Not make turnpoints! Please choose the flight type you want to submit </p>";

    flush();
}
?>
