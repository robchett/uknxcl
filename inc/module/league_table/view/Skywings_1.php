<?php
function cmp($a, $b) {
    if ($a->Score == $b->Score) {
        return 0;
    }
    return ($a->Score > $b->Score) ? -1 : 1;
}

$WHERE = "WHERE Season=2011 AND Score>10";
$WHERE = str_replace("'", "'", $WHERE);
$flights = 6;
$official = 1;
$split = 0;
$sql = "SELECT pid,pilot.name ,club.name ,G_CLASS ,glider.name,Score,Defined,Launch,Type FROM flight
        LEFT JOIN glider ON flight.gid=glider.gid
        LEFT JOIN club ON flight.cid=club.cid
        LEFT JOIN pilots ON flight.pid=pilots.pid
        $WHERE AND Delay=0 AND Personal=0 ORDER BY Score DESC";
$title = "";
$WHERE = str_replace("'", "\"", $WHERE);
$WHERE = str_replace(">'", '%3E', $WHERE);
$result = execute($sql);
$num = mysql_num_rows($result);
$array = array();
for ($i = 0; $i < $num; $i++) {
    $t = mysql_fetch_assoc($result);
    if ($split == 1) {
        if ($t ['Class'] == 5) {
            $t ['pid'] += 8000;
        }
    }
    if (isset ($array [$t ['pid']]))
        $array [$t ['pid']]->add_flight($t);
    else {
        $array [$t ['pid']] = new pilot_official ($t, $flights, $split);
        $array [$t ['pid']]->$output_function = 'csv';
    }
}
if (isset ($array)) {
    usort($array, "cmp");
    echo '<table>
    <th>pos</th><th>name</th><th>glider</th><th>club</th><th>1st</th><th>2nd</th><th>3rd</th><th>4th</th><th>5th</th><th>6th</th><th>total</th><th>Grand total</th>';
    //if(1)ShowTop4W($WHERE);
    $class1 = 1;
    $class5 = 1;
    for ($j = 0; $j < count($array); $j++) {
        if ($array [$j]->Class == 1) {
            echo $array [$j]->Output($class1, 0);
            $class1++;
        } else {
            echo $array [$j]->Output($class5, 0);
            $class5++;
        }
    }
}
echo '</table>';
mysql_close();
?>