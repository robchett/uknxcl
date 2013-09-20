<?php
/*class track_point
{
    public $time = 0;
    public $lat = 0;
    public $lon = 0;
    public $val = 0;
    public $alt = 0;
    public $ele = 0;
    public $sin_lat = 0;
    public $cos_lat = 0;
    public $lonRad = 0;
    public $bestBack;
    public $bestFwrd;
    public $climbRate = 0;
    public $speed = 0;

}

class track_t
{
    public $number_of_track_points = 0;
    public $track_points;
    public $distance_map;
    public $od;
    public $or;
    public $tr;
    public $maximum_distance_between_two_points;
    public $first_point_time;
    public $number_of_tracks = 1;
    public $track_parts;
    public $c;
    public $last_track_point;
    public $maximum_ele = -1000000;
    public $maximum_ele_t = 0;
    public $min_ele = 1000000;
    public $min_ele_t = 0;
    public $maximum_alt = -1000000;
    public $min_alt = 1000000;
    public $maximum_cr = 0;
    public $min_cr = 0;
    public $maximum_speed = 0;
    public $total_dist = 0;
    public $HasHeight = false;
    public $log = 0;
    public $log_file = "";

}

function match_h_record($p, &$track)
{
    if (substr($p, 0, 5) == "HFDTE") {
        $track->day = substr($p, 5, 2);
        $track->mon = substr($p, 7, 2);
        $track->year = "20" . substr($p, 9, 2);
        $track->date = "$track->year-$track->mon-$track->day";
        //console("Date recorded as : $track->date",$track,1,1);
    }
}

function match_b_record($p, &$track, &$start)
{
    $pos = 1;
    $track_point = new track_point ();
    // Extract time data
    $track_point->time = mktime(substr($p, $pos, 2), substr($p, $pos + 2, 2), substr($p, $pos + 4, 2));
    $pos += 6;
    // Extract latitude data
    $lat = round(substr($p, $pos, 2) + substr($p, $pos + 2, 5) / 60000, 6);
    $track_point->lat = (substr($p, $pos + 7, 1) == 'N') ? $lat : -1 * $lat;
    $track_point->sin_lat = sin(M_PI * $track_point->lat / 180);
    $track_point->cos_lat = cos(M_PI * $track_point->lat / 180);
    $pos += 8;
    // Extract longitude data
    $lon = round(substr($p, $pos, 3) + (substr($p, $pos + 3, 5) / 60000), 6);
    $track_point->lon = (substr($p, $pos + 8, 1) == 'E') ? $lon : -1 * $lon;
    $track_point->lonRad = M_PI * $track_point->lon / 180;
    $pos += 9;
    // Get height data
    $track_point->val = (int)substr($p, $pos, 1);
    $pos += 1;
    $track_point->alt = (int)substr($p, $pos, 5);
    $pos += 5;
    $track_point->ele = (int)substr($p, $pos, 5);
    $pos += 5;
    // Compare heights with max/min
    if ($track_point->ele > $track->maximum_ele) {
        $track->maximum_ele = $track_point->ele;
        $track->maximum_ele_t = $track_point->time - $track->first_point_time;
    }
    if ($track_point->ele < $track->min_ele) {
        $track->min_ele = $track_point->ele;
        $track->min_ele_t = $track_point->time - $track->first_point_time;
    }
    if ($track_point->alt > $track->maximum_alt) {
        $track->maximum_alt = $track_point->alt;
        $track->maximum_alt_t = $track_point->time - $track->first_point_time;
    }
    if ($track_point->alt < $track->min_alt) {
        $track->min_alt = $track_point->alt;
        $track->min_alt_t = $track_point->time - $track->first_point_time;
    }
    // Fill in troughs in height data. i.e lost gps signal
    if ($track->HasHeight && $track_point->ele == 0) {
        //console("Filled in trough  : 0 ele", $track);
        $track_point->ele = $track->last_track_point->ele;
    }
    // Flatten spikes in height data due to incorrect gps reading.
    if ($track->HasHeight && $track_point->ele > $track->last_track_point->ele + 500) {
        //console("Flattened peak  : {$track->last_track_point->ele} -> $track_point->ele", $track);
        $track_point->ele = $track->last_track_point->ele;
    }
    // Calculate climb rate
    if ($track->HasHeight) {
        if ($track_point->time - $track->last_track_point->time) {
            $track_point->climbRate = ($track_point->ele - $track->last_track_point->ele) / ($track_point->time - $track->last_track_point->time);
            if ($track_point->climbRate < $track->min_cr) $track->min_cr = $track_point->climbRate;
            if ($track_point->climbRate > $track->maximum_cr) $track->maximum_cr = $track_point->climbRate;
        } else $track_point->climbRate = 0;
    } else $track_point->climbRate = 0;
    // Look for height data
    if ($track_point->ele != 0) {
        $track->HasHeight = true;
    }
    // calculate speed
    if ($track->last_track_point) {
        if ($track_point->time - $track->last_track_point->time) {
            $x = $track_point->sin_lat * $track->last_track_point->sin_lat + $track_point->cos_lat * $track->last_track_point->cos_lat * cos($track_point->lonRad - $track->last_track_point->lonRad);
            $track_point->speed = (int)(acos($x) * 6371000) / ($track_point->time - $track->last_track_point->time);
            $track->total_dist += (acos($x) * 6371);
            if ($track_point->speed > $track->maximum_speed) $track->maximum_speed = $track_point->speed;
        } else $track_point->speed = 0;
    } else $track_point->speed = 0;
    // set first point time if not done already
    if (!isset ($track->first_point_time)) {
        $track->first_point_time = $track_point->time;
    } elseif ($track_point->time - $track->last_track_point->time > 60 || $track_point->time - $track->last_track_point->time < 0) {
        $track->track_parts [] = Array('Start' => $start, 'End' => $track->ntrack_points - 1, 'Time' => getTime($track->track_points [$start]->time) . '-' . getTime($track->last_track_point->time));
        $start = $track->ntrack_points;
        $track->number_of_tracks++;
    }
    $track->track_points [] = $track_point;
    $track->ntrack_points++;
    $track->last_track_point = $track_point;
}

function parse_IGC($id, $log = 0, $raw = 0)
{
    $id = (int)$id;
    $track = new track_t ();
    if (file_exists("./Tracks/$id/Track_log.igc")) {
        $pre = './';
    } else if (file_exists("../Tracks/$id/Track_log.igc")) {
        $pre = '../';
    } else if (file_exists("../../Tracks/$id/Track_log.igc")) {
        $pre = '../../';
    } else {
        echo "IGC Could not be found.";
        return false;
    }
    $file = file($pre . "Tracks/$id/Track_log.igc");
    $track->pre = $pre;
    $track->log = $log;
    $track->raw = $raw;
    console("Flight Read", $track, 1, 1);
    $i = 0;
    $start = 0;
    while (isset ($file [$i])) {
        $record = $file [$i];
        switch (substr($record, 0, 1)) {
            case 'B' :
                match_b_record($file [$i], $track, $start);
                break;
            case 'H' :
                match_h_record($file [$i], $track);
                break;
        }
        $i++;
    }
    $track->track_parts [] = Array('Start' => $start, 'End' => $track->ntrack_points - 1, 'Time' => getTime($track->track_points [$start]->time) . '-' . getTime($track->last_track_point->time));
    $track->TotalTime = $track->last_track_point->time - $track->first_point_time;

    //code to remove parts with minimal distance & points.
    if (0) {
        while (1) {
            $noOfPoints = $track->track_parts[0]['End'] - $track->track_parts[0]['Start'];
            if ($noOfPoints < 20) {
                console("Begining section ignored, less than 20 points", $track, 1, 1);
                $track->track_parts = shuffledown($track->track_parts);
                $track->number_of_tracks--;
                continue;
            }
            Point = round(($track->track_parts[0]['End'] + $track->track_parts[0]['Start']) / 2);
            $coord1 = $track->track_points[$track->track_parts[0]['Start']];
            $coord2 = $track->track_points[Point];
            $coord3 = $track->track_points[$track->track_parts[0]['End']];
            if ((acos($coord1->sin_lat * $coord2->sin_lat + $coord1->cos_lat * $coord2->cos_lat * cos($coord1->lonRad - $coord2->lonRad))
                + acos($coord2->sin_lat * $coord3->sin_lat + $coord2->cos_lat * $coord3->cos_lat * cos($coord2->lonRad - $coord3->lonRad))) * 6371000
                < 200
            ) {
                console("Begining section ignored, less than 100m", $track, 1, 1);
                $track->track_parts = shuffledown($track->track_parts);
                $track->number_of_tracks--;
            } else break;
        }
        while (1) {
            $noOfPoints = $track->track_parts[$track->number_of_tracks - 1]['End'] - $track->track_parts[$track->number_of_tracks - 1]['Start'];
            if ($noOfPoints < 20) {
                console("End section ignored, less than 20 points", $track, 1, 1);
                unset($track->track_parts[$track->number_of_tracks - 1]);
                $track->number_of_tracks--;
                continue;
            }
            Point = round(($track->track_parts[$track->number_of_tracks - 1]['End'] + $track->track_parts[$track->number_of_tracks - 1]['Start']) / 2);
            $coord1 = $track->track_points[$track->track_parts[$track->number_of_tracks - 1]['Start']];
            $coord2 = $track->track_points[Point];
            $coord3 = $track->track_points[$track->track_parts[$track->number_of_tracks - 1]['End']];
            if ((acos($coord1->sin_lat * $coord2->sin_lat + $coord1->cos_lat * $coord2->cos_lat * cos($coord1->lonRad - $coord2->lonRad))
                + acos($coord2->sin_lat * $coord3->sin_lat + $coord2->cos_lat * $coord3->cos_lat * cos($coord2->lonRad - $coord3->lonRad))) * 6371000
                < 200
            ) {
                console("End section ignored, less than 100m", $track, 1, 1);
                unset($track->track_parts[$track->number_of_tracks - 1]);
                $track->number_of_tracks--;
            } else break;
        }
    }
    return $track;
}

function getTime($time)
{
    $time -= mktime(0, 0, 0);
    return date('H:i:s', $time);
}

function shuffledown($array)
{
    $i = sizeof($array);
    $array2 = array();
    for ($x = 0; $x < $i - 1; $x++) {
        $array2[$x] = $array[$x + 1];
    }
    return $array2;
}

function mod60000($a)
{
    return round($a / 60000, 6);
}

function distCalc($cordset, $track)
{
    $dist = 0;
    for ($i = 0; $i < sizeof($cordset) - 1; $i++) {
        //  echo $track->distance_map[$cordset[$i]][$cordset[$i+1]];
        $dist += $track->distance_map[$cordset[$i]][$cordset[$i + 1]];
    }
    return $dist / 1000;
}

function cordsCalc($a, $track)
{
    $out = "";
    foreach ($a as $cord) {
        $out .= LatLongToOSGrid(($track->c[$cord]->lat), ($track->c[$cord]->lon));
        $out .= ";";
    }
    return substr($out, 0, -1);
}

function getBounds($set)
{
    $lat1 = $set[0]->lat;
    $lat2 = $set[0]->lat;
    $lon1 = $set[0]->lon;
    $lon2 = $set[0]->lon;
    unset($set[0]);
    foreach ($set as $point) {
        if ($point->lat < $lat1) $lat1 = $point->lat;
        else if ($point->lat > $lat2) $lat2 = $point->lat;
        if ($point->lon < $lon1) $lon1 = $point->lon;
        else if ($point->lon > $lon2) $lon2 = $point->lon;
    }
    return Array($lat1, $lat2, $lon1, $lon2);
}

function getDiag($bounds, $sin, $cos)
{
    return acos($sin[0] * $sin[1] + $cos[0] * $cos[1] * cos($bounds[2] * M_PI / 180 - $bounds[3] * M_PI / 180));
}

function use_opt_triangle($set1, $set2, $set3, $ll)
{
    // increasing l->r
    if ($set2->bounds[$ll] > $set1->bounds[$ll] && $set2->bounds[$ll + 1] < $set3->bounds[$ll + 1]) {

    }
    // decreasing l->r
    if ($set2->bounds[$ll] > $set3->bounds[$ll] && $set2->bounds[$ll + 1] < $set1->bounds[$ll + 1]) {

    }
    // mid high
    if ($set2->bounds[$ll + 1] > $set3->bounds[$ll] && $set2->bounds[$ll + 1] < $set1->bounds[$ll + 1]) {

    }
}

function use_brute_triangle($set1, $set2, $set3)
{
    $map_2_3 = array();
    $map_1_2 = array();
    $map_3_1 = array();
    $maximum_score = 0;
    for ($i = 0; $i < 4; $i++) {
        for ($j = 0; $j < 4; $j++) {
            $map_1_2[$i][$j] = acos($set1->sinlat[$i % 2] * $set2->sinlat[$j % 2] + $set1->coslat[$i % 2] * $set2->coslat[$j % 2] * cos($set1->bounds[($i % 2) + 2] * M_PI / 180 - $set2->bounds[($j % 2) + 2] * M_PI / 180)) * 6371;
        }
    }
    for ($i = 0; $i < 4; $i++) {
        for ($j = 0; $j < 4; $j++) {
            $map_2_3[$i][$j] = acos($set2->sinlat[$i % 2] * $set3->sinlat[$j % 2] + $set2->coslat[$i % 2] * $set3->coslat[$j % 2] * cos($set2->bounds[($i % 2) + 2] * M_PI / 180 - $set3->bounds[($j % 2) + 2] * M_PI / 180)) * 6371;
        }
    }
    for ($i = 0; $i < 4; $i++) {
        for ($j = 0; $j < 4; $j++) {
            $map_3_1[$i][$j] = acos($set1->sinlat[$i % 2] * $set3->sinlat[$j % 2] + $set1->coslat[$i % 2] * $set3->coslat[$j % 2] * cos($set1->bounds[($i % 2) + 2] * M_PI / 180 - $set3->bounds[($j % 2) + 2] * M_PI / 180)) * 6371;
        }
    }
    for ($i = 0; $i < 4; $i++) {
        for ($j = 0; $j < 4; $j++) {
            for ($k = 0; $k < 4; $k++) {
                $score = $map_1_2[$i][$j] + $map_2_3[$j][$k] + $map_3_1[$k][$i];
                if (is_nan($score)) {
                    echo "score not a number {$map_1_2[$i][$j]}+ {$map_2_3[$j][$k]}+{$map_3_1[$k][$i]} \r\n ";
                } else if ($score > $maximum_score && min($map_1_2[$i][$j], $map_2_3[$j][$k], $map_3_1[$k][$i]) > 0.28 * $score)
                    $maximum_score = $map_1_2[$i][$j] + $map_2_3[$j][$k] + $map_3_1[$k][$i];
            }
        }
    }
    return $maximum_score;
}

function rebound(&$track)
{
    $n_groups = sizeof($track->sets);
    while ($n_groups < 30) {
        // find highest diagonal in a set.
        $largest_set = -1;
        $largest_dia = -0;
        foreach ($track->sets as $set) {
            if ($set->diag > $largest_dia) {
                $largest_dia = $set->diag;
                $largest_set = $set->id;
            }
        }
        // split the largest set in half.
        $count = 0;
        foreach ($track->sets as $set) {
            if ($set->id != $largest_set) {
                $newSets[] = $set;
                $newSets[$count]->id = $count;
                $count++;
            } else {
                $count_2 = 0;
                $newSets[] = new stdClass();
                $size = (int)$set->size / 2;
                if ($set->size != 1) {
                    for ($j = 0; $j < $size; $j++) {
                        $newSets[$count]->points[] = $set->points[$count_2];
                        $count_2++;
                    }
                    $newSets[$count]->size = sizeof($newSets[$count]->points);
                    $newSets[$count]->bounds = getBounds($newSets[$count]->points);
                    $newSets[$count]->sinlat = Array(sin($newSets[$count]->bounds[0] * M_PI / 180), sin($newSets[$count]->bounds[1] * M_PI / 180));
                    $newSets[$count]->coslat = Array(cos($newSets[$count]->bounds[0] * M_PI / 180), cos($newSets[$count]->bounds[1] * M_PI / 180));
                    $newSets[$count]->diag = getDiag($newSets[$count]->bounds, $newSets[$count]->sinlat, $newSets[$count]->coslat);
                    $newSets[$count]->score = 0;
                    $newSets[$count]->id = $count;
                    $count++;
                    $newSets[] = new stdClass();
                    for ($j = $count_2; $j < $set->size; $j++) {
                        $newSets[$count]->points[] = $set->points[$count_2];
                        $count_2++;
                    }
                    $newSets[$count]->size = sizeof($newSets[$count]->points);
                    $newSets[$count]->bounds = getBounds($newSets[$count]->points);
                    $newSets[$count]->sinlat = Array(sin($newSets[$count]->bounds[0] * M_PI / 180), sin($newSets[$count]->bounds[1] * M_PI / 180));
                    $newSets[$count]->coslat = Array(cos($newSets[$count]->bounds[0] * M_PI / 180), cos($newSets[$count]->bounds[1] * M_PI / 180));
                    $newSets[$count]->diag = getDiag($newSets[$count]->bounds, $newSets[$count]->sinlat, $newSets[$count]->coslat);
                    $newSets[$count]->score = 0;
                    $newSets[$count]->id = $count;
                    $count++;
                } else {
                    $newSets[$count]->points[] = $set->points[0];
                    $newSets[$count]->size = sizeof($newSets[$count]->points);
                    $newSets[$count]->bounds = getBounds($newSets[$count]->points);
                    $newSets[$count]->diag = 0;
                    $newSets[$count]->sinlat = Array(sin($newSets[$count]->bounds[0] * M_PI / 180), sin($newSets[$count]->bounds[1] * M_PI / 180));
                    $newSets[$count]->coslat = Array(cos($newSets[$count]->bounds[0] * M_PI / 180), cos($newSets[$count]->bounds[1] * M_PI / 180));
                    $newSets[$count]->score = 0;
                    $newSets[$count]->id = $count;
                    $count++;
                }
            }
        }
        $track->sets = $newSets;
        unset($newSets);
        $n_groups = sizeof($track->sets);
    }
}

function trim_sets(&$track)
{
    $n_groups = sizeof($track->sets);
    $id_delete_prev = -1;
    $id_delete = 0;
    for ($i = 0; $i <= $n_groups; $i++) {
        $low_score = $track->high + 1;
        foreach ($track->sets as $set) {
            if ($set->id == $id_delete_prev) {
                unset($track->sets[$id_delete_prev]);
            }
            if ($set->score < $low_score) {
                $low_score = $set->score;
                $id_delete = $set->id;
            }
        }
        $id_delete_prev = $id_delete;
    }
}

function calculate(&$track, $od_m = 1, $or_m = 2, $tr_m = 2.5)
{
    set_time_limit(0);

    // form the initial set.
    $n_groups = 30;
    $t_sets = floor($track->ntrack_points / $n_groups);
    console("$n_groups sets with $t_sets each", $track, 1, 1);
    $iterations = 0;
    $t = $t_sets;
    while (($t = (floor($t / 2))) > 1) $iterations++;
    echo $iterations;


    $track->sets[] = new stdClass();
    foreach ($track->track_points as $track_point) {
        $track->sets[0]->points[] = $track_point;
    }
    $track->sets[0]->id = 0;
    $track->sets[0]->size = sizeof($track->sets[0]->points);
    $track->sets[0]->bounds = getBounds($track->sets[0]->points);
    $track->sets[0]->sinlat = Array(sin($track->sets[0]->bounds[0] * M_PI / 180), sin($track->sets[0]->bounds[1] * M_PI / 180));
    $track->sets[0]->coslat = Array(cos($track->sets[0]->bounds[0] * M_PI / 180), cos($track->sets[0]->bounds[1] * M_PI / 180));
    $track->sets[0]->diag = getDiag($track->sets[0]->bounds, $track->sets[0]->sinlat, $track->sets[0]->coslat);
    $track->sets[0]->score = 0;
    //print_r($track->sets);
    console("Initial Formed", $track, 1, 1);

    rebound($track);

    console("Set branched", $track, 1, 1);

    // initial calculations for trinalges
    console("Calculating triangles", $track, 1, 1);

    $iterations = 1;
    for ($count = 0; $count <= $iterations; $count++) {
        $n_groups = sizeof($track->sets);
        $high_score = $set1 = $set2 = $set3 = 0;
        for ($i = 0; $i < $n_groups; $i++) {
            for ($j = $i + 1; $j < $n_groups; $j++) {
                for ($k = $j + 1; $k < $n_groups; $k++) {
                    $score = use_brute_triangle($track->sets[$i], $track->sets[$j], $track->sets[$k]);
                    if ($score > $high_score) {
                        $high_score = $score;
                        $set1 = $i;
                        $set2 = $j;
                        $set3 = $k;
                    }
                    if ($score > $track->sets[$i]->score) $track->sets[$i]->score = $score;
                    if ($score > $track->sets[$j]->score) $track->sets[$j]->score = $score;
                    if ($score > $track->sets[$k]->score) $track->sets[$k]->score = $score;
                }
            }
        }
        $track->high = $high_score;

        if ($count == $iterations) {
            console("triangles calculated", $track, 1, 1);
            console("high score:$track->high", $track, 1, 1);
            echo "<script language='javascript' type='text/javascript'>";
            foreach ($track->sets as $set) {
                echo "window.top.window.map.addRectangle({$set->bounds[0]},{$set->bounds[1]},{$set->bounds[2]},{$set->bounds[3]},0,{$set->score});";
            }
            echo "window.top.window.map.addRectangle({$track->sets[$set1]->bounds[0]},{$track->sets[$set1]->bounds[1]},{$track->sets[$set1]->bounds[2]},{$track->sets[$set1]->bounds[3]},1,{$track->sets[$set1]->score});";
            echo "window.top.window.map.addRectangle({$track->sets[$set2]->bounds[0]},{$track->sets[$set2]->bounds[1]},{$track->sets[$set2]->bounds[2]},{$track->sets[$set2]->bounds[3]},1,{$track->sets[$set2]->score});";
            echo "window.top.window.map.addRectangle({$track->sets[$set3]->bounds[0]},{$track->sets[$set3]->bounds[1]},{$track->sets[$set3]->bounds[2]},{$track->sets[$set3]->bounds[3]},1,{$track->sets[$set3]->score});";
            echo "</script>";
            return;
        }

        trim_sets($track);
        rebound($track);
        echo "pass $count complete";
    }

}

function info_log(&$track, $str)
{
    $track->log .= $str;
}


function console($str, &$track, $script = 0, $raw = 0)
{
    if ($script && $track->log) {
        echo "<script language='javascript' type='text/javascript'>window.top.window.console_log(\"$str\")</script>";
        echo str_repeat(' ', 1024 * 64) . '
';
        flush();
    }
    if ($raw && $track->raw) {
        echo "$str<br/>";
        //echo str_repeat(' ',1024*64);
        flush();
    }
    $track->log_file .= "$str\r\n";
}

*/
