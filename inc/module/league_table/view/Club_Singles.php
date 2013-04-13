<?php

function makeTable(league_table $data) {
    $html = '';
    makeHeader();
    $Club = $_GET ['club'];
    $launch = isset($_GET['launch']) ? "Launch=$_GET[launch] AND " : '';
    if (isset ($_GET ['year'])) {
        $Year = $_GET ['year'];
        $WHERE = "WHERE Season=$Year AND $launch";
        $WHERE2 = "WHERE Season=$Year AND cid=$Club $launch";
    } else {
        $Year = "All Time";
        $WHERE = "WHERE $launch";
        $WHERE2 = "WHERE cid=$Club $launch";
    }

    $club_name = SelectOne("club.name", "club", "cid=$Club");
    $flights = isset($_GET['flights']) ? $_GET['flights'] : 6;
    $sql = "SELECT pid,gid,pilot.name,C_Name,class,G_Name,Manufacturer,flight.* FROM flight
        LEFT JOIN glider ON flight.gid=glider.gid
        LEFT JOIN club ON flight.cid=club.cid
        LEFT JOIN pilots ON flight.pid=pilots.pid
        $WHERE Delay=0 AND Personal=0 ORDER BY Score DESC";
    $pilots = 2000;
    $result = execute($sql);
    $num = mysql_num_rows($result);
    $Pilotarray = array();
    for ($i = 0; $i < $num; $i++) {
        $t = mysql_fetch_assoc($result);
        if (isset ($Pilotarray [$t ['pid']]))
            $Pilotarray [$t ['pid']]->add_flight($t);
        else
            $Pilotarray [$t ['pid']] = new pilot ($t, $flights, 0);
    }
    if (isset ($Pilotarray))
        usort($Pilotarray, "cmp");

    $Clubarray = array();
    for ($i = 0; $i < count($Pilotarray); $i++) {
        if (isset ($Clubarray [$Pilotarray [$i]->Club]))
            $Clubarray [$Pilotarray [$i]->Club]->AddSub($Pilotarray [$i], $flights);
        else
            $Clubarray [$Pilotarray [$i]->Club] = new Club ($Pilotarray [$i], $pilots, $flights);
    }
    if (isset ($Clubarray))
        usort($Clubarray, "cmp");

    echo "  <table class=\"c\"><th class=\"c\"style=\"width:684px\">$club_name - $Year</th></table>";
    for ($j = 0; $j < count($Clubarray); $j++) {
        if ($Clubarray [$j]->Name == $club_name) {
            echo $Clubarray [$j]->writeClubSemiHead($j + 1);
            $html .= WriteTableHead($flights);
            echo $Clubarray [$j]->Pilot;
            echo "</table>";
        }
    }

    return $html;
}

?>