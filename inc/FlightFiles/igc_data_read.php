<?php
set_time_limit(0);
include('../phpsqlajax_dbinfo.php');
include('../FlightFiles/IGC_parse.php');
include('../FlightFiles/file_convert.php');
$result = execute('SELECT fid FROM flight WHERE dim<>1 AND fid>8221');

while (($t = mysql_fetch_array($result)) != null) {
    if (file_exists("../Tracks/$t[0]/Track_log.igc")) {
        $track = parse_IGC($t[0]);
        if (!$track) continue;

        echo "$track->total_dist<br/>";
        // check that it fits the number of tracks criterion
        //if ($track->get_number_of_tracks() !=1){
        //    echo "$t[0]~$track->get_number_of_tracks()~";
        //    foreach($track->track_parts as $a){
        //        echo "~$a[Time]~";
        //    }
        //    echo "<br/>";
        //}

        //
        // check its 3d validity
        ///ThreeDimCheck($track,$t);

        // calculate flight time
        //CalculateFlightTime($track,$t);

        // other things to set.

        // add data for each flight type;
        calculate($track);
        //execute("UPDATE flight SET
        //            Flighttime = {$track->TotalTime},
        //            ODs={$track->od[5]},
        //            ODt={$track->od[6]}, 
        //            ORs={$track->or[4]},
        //            ORt={$track->or[5]},
        //            TRs={$track->tr[5]},
        //            TRt={$track->tr[6]}
        //        WHERE fid=$t[0]");
        $track->generate_kml();
        $track->generate_js();
        $track->generate_kml_earth();
    } else echo "$t[0] is missing a trace <br/>";
}

function ThreeDimCheck($track, $t) {
    if (($track->maximum_alt != $track->min_alt) || ($track->maximum_ele != $track->min_ele)) {
        execute("UPDATE flight SET dim=3 WHERE fid=$t[0]");
    } else execute("UPDATE flight SET dim=2 WHERE fid=$t[0]");
}

function CalculateFlightTime($track, $t) {
    $time = -$track->first_point_time + $track->last_track_point->time;
    execute("UPDATE flight SET flighttime=$time WHERE fid=$t[0]");
}

function create_kml($id, $track, $prepend) {
    $result = execute("SELECT pilot.name as Pilot,club.name as Club,G_CLASS as Class,glider.name as Glider,Score FROM flight
        LEFT JOIN glider ON flight.gid=g.gid
        LEFT JOIN club ON flight.cid=c.cid
        LEFT JOIN pilots ON flight.pid=p.pid
     pid   WHERE fid=$id LIMIT 1"
    );
    $t = db::fetch($result);

    //$date = date('Y-m-d',$track->first_point_time);
    $startTime = date('H:i:s', $track->first_point_time);
    $endTime = date('H:i:s', $track->last_track_point->time);
    $duration = date('H:i:s', $track->last_track_point->time - $track->first_point_time);
    $track->totalTime = $track->last_track_point->time - $track->first_point_time;
    $maxEle = $track->maximum_ele;
    $minEle = $track->min_ele;
    foreach ($track->track_points as $a) {
        if ($a->ele > $minEle)
            continue;
        $minEle = $a->ele;
    }
    $pilot = $t ['Pilot'];
    $track->name = $pilot;
    $club = $t ['Club'];
    $glider = $t ['Glider'];
    $score = $t ['Score'];
    $col = "FFBBCC00";
    $output = "<?xml version='1.0' encoding='UTF-8'?>
<Document>
    <Placemark>
      <name>Flight $id</name>
      <description><![CDATA[
        <pre>
        Flight statistics
        Flight #             $id
        Pilot                $pilot
        Club                 $club
        Glider               $glider
        Date                 $track->date
        Start/finish         $startTime - $endTime
        Duration             $duration
        Max./min. height     $maxEle / $minEle m
        Score                $score
        </pre>]]>
      </description>
      <Style>
        <LineStyle>
          <color>$col</color>
          <width>2</width>
        </LineStyle>
      </Style>
      <Metadata src='UKNXCL' v='0.9' type='track'>
        <SecondsFromTimeOfFirstPoint>";
    $count = 0;
    foreach ($track->track_points as $out1) {
        if (is_int($count / 15))
            $output .= "\n";
        $count++;
        $output .= $out1->time - $track->first_point_time . " ";
    }
    $output .= "
        </SecondsFromTimeOfFirstPoint>
      </Metadata>
      <LineString>
        <coordinates>";
    $count = 0;
    foreach ($track->track_points as $out1) {
        if (is_int($count / 5))
            $output .= "\n";
        $count++;
        $output .= $out1->lon . "," . $out1->lat . ",$out1->ele ";
    }
    $output .= "
        </coordinates>
      </LineString>
    </Placemark>
    <Placemark>
        <description><![CDATA[
        <pre>
        Open Distance
        Duration             {$track->od[6]}
        Score                {$track->od[5]}km
        </pre>]]>
      </description>
        <Style>
            <LineStyle>
                <color>FF000000</color>
                <width>2</width>
            </LineStyle>
        </Style>
        <LineString>
            <coordinates>
                {$track->choosenCord[$track->od[0]]->lon},{$track->choosenCord[$track->od[0]]->lat},{$track->choosenCord[$track->od[0]]->ele} 
                {$track->choosenCord[$track->od[1]]->lon},{$track->choosenCord[$track->od[1]]->lat},{$track->choosenCord[$track->od[1]]->ele} 
                {$track->choosenCord[$track->od[2]]->lon},{$track->choosenCord[$track->od[2]]->lat},{$track->choosenCord[$track->od[2]]->ele} 
                {$track->choosenCord[$track->od[3]]->lon},{$track->choosenCord[$track->od[3]]->lat},{$track->choosenCord[$track->od[3]]->ele} 
                {$track->choosenCord[$track->od[4]]->lon},{$track->choosenCord[$track->od[4]]->lat},{$track->choosenCord[$track->od[4]]->ele} 
            </coordinates>
        </LineString>
      </Placemark>
</Document>";
    $outFile = fopen("$prepend/Tracks/$id/Track.kml", 'w');
    fwrite($outFile, $output);
    return $track;
}

function create_js($id, $track, $prepend) {
    $sql = "SELECT date,Defined,Launch,Type,Cords,Base_Score,Multi,Score,Ridge,pilot.name,club.name,glider.name,Vis_Info FROM flight
                        LEFT JOIN glider ON flight.gid=glider.gid
                        LEFT JOIN club ON flight.cid=club.cid
                        LEFT JOIN pilots ON flight.pid=pilots.pid
                WHERE flight.fid=$id LIMIT 1";
    $result = execute($sql);
    $t = db::fetch($result);
    $graph1 = "";
    $array = array();
    foreach ($track->track_points as $tp) {
        $n = count($array);
        $array [$n]->time = $tp->time;
        $array [$n]->lat = $tp->lat;
        $array [$n]->lon = $tp->lon;
        $array [$n]->ele = $tp->ele;
    }
    $changeT = $track->last_track_point->time - $track->first_point_time;

    $outFile = fopen("$prepend/Tracks/$id/Track.js", 'w');
    fwrite($outFile, "
var track= new Object(); 
track.id = $id;
track.pilot_name = '$t[pilot_name]';
track.colour = 'FF0000';
track.maximum_height =$track->maximum_ele;
track.min_height =$track->min_ele;
track.time = $changeT;
track.coords = ["
    );

    $out = '';
    foreach ($array as $a) {
        $time = $a->time - $track->first_point_time;
        $out .= "[$a->lat,$a->lon,$a->ele,$time],";
    }
    fwrite($outFile, substr($out, 0, -1) . '];');
}

?>

