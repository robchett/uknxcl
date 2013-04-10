<?php
function distCalc($cordset, $track) {
    $dist = 0;
    for ($i = 0; $i < sizeof($cordset) - 1; $i++) {
        //  echo $track->distance_map[$cordset[$i]][$cordset[$i+1]];
        $dist += $track->distance_map[$cordset[$i]][$cordset[$i + 1]];
    }
    return $dist / 1000;
}

function calculate(&$track, $od_m = 1, $or_m = 2, $tr_m = 2.5) {
    set_time_limit(0);
    $t = memory_get_usage();
    // Work out the best "legs" moving forward and backwards.
    // Using the dist and best leg work out the greatest distance with 1 turnpoint behind the point used as key.
    // [0] is dist, [1] is turn point, [2] is end point (or start point as it actualy is)
    $t = microtime(1);
    $track->od = track_open_distance_3tp($track);
    $t -= microtime(1);
    console("Open Distance Calculated, t=$t Dist:{$track->od[5]} Cords={$track->od[7]}", $track, 0, 1);
    $t = microtime(1);
    console("OD calculated", $track, 1, 1);
    $track->or = track_out_and_return($track);
    $t -= microtime(1);
    console("Out and Return Calculated, t=$t Dist:{$track->or[4]}Cords={$track->or[6]}", $track, 0, 1);
    $t = microtime(1);
    console("OR calculated", $track, 1, 1);
    if (!isset($_POST['skip_tr'])) {
        $track->tr = track_triangles($track);
        console("TR calculated", $track, 1, 1);
        $t -= microtime(1);
        console("Triange Calculated, t=$t Dist:{$track->tr[5]}Cords={$track->tr[7]}", $track, 0, 1);
    } else {
        $track->tr = array(0, 0, 0, 0, 0, 0, 0);
    }
    if ($track->od[5] * $od_m > $track->or[4] * $or_m && $track->od[5] * $od_m > $track->tr[5] * $tr_m) {
        $track->TopCords = $track->od[7];
        $track->TopType = 0;
        $track->TopScore = $track->od[5];
    } else if ($track->or[4] * $or_m > $track->tr[5] * $tr_m) {
        $track->TopCords = $track->or[6];
        $track->TopType = 1;
        $track->TopScore = $track->or[4];
    } else {
        $track->TopCords = $track->tr[7];
        $track->TopType = 3;
        $track->TopScore = $track->tr[5];
    }
    console("Hightst Score calculated", $track);
    unset($track->distance_map);
}

?>