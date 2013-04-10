<?php

$result = execute("SELECT ID FROM flight ORDER BY ID DESC LIMIT 1");
$id = db::query($result);
$id = $id [0] + 1;
db::query("INSERT INTO flight (ID,pid,cid,gid) VALUES ($id,17,1,1)");
$track = new track();
$track->day = substr($_POST ['date'], 0, 2);
$track->mon = substr($_POST ['date'], 3, 2);
$track->year = substr($_POST ['date'], 6, 4);
$track->date = "$track->year-$track->mon-$track->day";
$track->TotalTime = 0;
$track->od[5] = 0;
$track->od[6] = 0;
$track->or[4] = 0;
$track->or[5] = 0;
$track->tr[5] = 0;
$track->tr[6] = 0;
add($id, $track, 1);
$score = $_POST['dist'] * $_POST['multi'];
execute("UPDATE flight SET Type = $_POST[type],
                            Base_Score = $_POST[dist], 
                            Score = $score, 
                            Cords = '$_POST[cords]', 
                            Multi = $_POST[multi] 
        WHERE ID=$id"
);
echo"    <script language='javascript' type='text/javascript'>
    window.top.window.stopUpload('Complete','WriteHereNewFlight4');
    window.top.window.reloadLatest();
</script>";

function add($id, $track, $dim1 = 0) {
    if ($dim1) $dim = 1;
    else $dim = (($track->maximum_alt != $track->min_alt) || ($track->maximum_ele != $track->min_ele)) ? 3 : 2;
    $launch = $_POST ['launch'];
    $vis = $_POST ['vis_info'];
    $invis = $_POST ['invis_info'];
    $defi = (isset ($_POST ["defined"])) ? 1 : 0;
    $ridge = (isset ($_POST ["ridge"])) ? 1 : 0;
    $personal = (isset ($_POST ["personal"])) ? 1 : 0;
    $delay = (isset ($_POST ['delay'])) ? 1 : 0;
    $winter = ($track->mon == 1 || $track->mon == 2 || $track->mon == 12) ? 1 : 0;
    $season = $track->year;
    if ($track->mon >= 11) $season++;
    $vis = str_replace("'", "''", $vis);
    $invis = str_replace("'", "''", $invis);
    echo $sql = "UPDATE flight SET  pid = $_POST[pilot],
                                cid =    $_POST[club],
                                gid =  $_POST[glider],
                                Date = '$track->date',
                                Season = $season, 
                                Launch = $launch, 
                                Defined = $defi,
                                Ridge = $ridge,
                                Winter = $winter,
                                Vis_Info = '$vis',
                                Admin_Info = '$invis',
                                Delay = $delay,
                                Personal = $personal,
                                Dim = $dim,
                                Flighttime = $track->TotalTime,
                                ODs = {$track->od[5]},
                                ODt = {$track->od[6]},
                                ORs = {$track->or[4]},
                                ORt = {$track->or[5]},
                                TRs = {$track->tr[5]},
                                TRt = {$track->tr[6]}
    WHERE ID=$id";
    execute($sql);
    mail('rob_chet@hotmail.com', "New flight added id#$id", 'A new flight has been added to the database.', 'From: <admin@uknxcl.co.uk>');
    mail('admin@eacomms.co.uk', "New flight added id#$id", 'A new flight has been added to the database.', 'From: <admin@uknxcl.co.uk>');

}

?>
