<?php
$t = execute("SELECT * FROM waypoints");
while (($s = mysql_fetch_assoc($t)) != false) {
    echo "map.addWaypoint($s[Lat],$s[Lon]);\r\n";
}
?>
