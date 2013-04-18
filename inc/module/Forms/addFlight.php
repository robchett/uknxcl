<?php
$end = "";
if (isset($_POST['typeChoice'])) {
    require '../../phpsqlajax_dbinfo.php';
    $id = $_POST ['id'];
    switch ($_POST ['type']) {
        case 0 :
            $score = $_POST ['odS'];
            $multi = $_POST ['odSm'];
            $scoer = $score * $multi;
            $cords = $_POST ['odSc'];
            break;
        case 1 :
            $score = $_POST ['orS'];
            $multi = $_POST ['orSm'];
            $scoer = $score * $multi;
            $cords = $_POST ['orSc'];
            break;
        case 3 :
            $score = $_POST ['trS'];
            $multi = $_POST ['trSm'];
            $scoer = $score * $multi;
            $cords = $_POST ['trSc'];
            break;
    }
    $del = (isset($_POST['delay'])) ? 1 : 0;
    //execute("UPDATE flight SET Type = $_POST[type], Base_Score = $score, Score = $scoer, Cords = '$cords', Multi = $multi, Delay=$del, Vis_Info = '$_POST[vis_info]', Admin_Info = '$_POST[invis_info]'  WHERE ID=$id");
    $end = "
        <script language='javascript' type='text/javascript'>
        window.top.window.stopUpload(document.getElementById('Result').innerHTML,'WriteHereNewFlight');
        window.top.window.map.add_flight($id,0,1);
        window.top.window.add_flightReset();
    </script>";
} else if (isset($_POST['cancel'])) {
    $end = "
        <script language='javascript' type='text/javascript'>
            window.top.window.stopUpload(document.getElementById('Result').innerHTML,'WriteHereNewFlight');
            window.top.window.add_flightReset();
        </script>";
}
echo $end;

function DateCheck($day, $mon, $year) {
    $date = "$year-$mon-$day";
    $current_time = date("Y-m-d");
    $closure_time = date("Y-m-d", time() - (31 * 24 * 60 * 60));
    if ($date >= $closure_time && $date <= $current_time)
        return true;
    else
        return false;
}

function igc_getDate($file) {
    $date = array();
    foreach ($file as $p)
        if (substr($p, 0, 5) == "HFDTE") {
            $date [] = substr($p, 5, 2);
            $date [] = substr($p, 7, 2);
            $date [] = substr($p, 9, 2);
            return $date;
        }
}

function igc_form_confirm($id, $track) {
    time_split_kml_plus_js($track, $id);
    $rig = "";
    $del = "";
    $per = "";
    if (isset ($_POST ['ridge'])) {
        $rig = "<input type='hidden' name='ridge' value='true'/>";
        $m = 0;
    }
    if (isset ($_POST ['delay'])) {
        $del = "Delay:<input type='checkbox' name='delay' checked=checked value='true'/><br/>";
    } else $del = "Delay:<input type='checkbox' name='delay' value='true'/><br/>";
    if (isset ($_POST ['personal'])) {
        $per = "Personal?<input type='hidden' name='personal' value='true'/>";
    }
    echo "";
}

function Split_IGC($start, $end, $fileName) {
    $file = file($fileName);
    $outFile = fopen($fileName, 'w');
    $i = 0;
    $b_record_count = 0;
    while (isset ($file [$i])) {
        $record = $file [$i];
        if ($record [0] == 'B') {
            if ($b_record_count >= $start && $b_record_count <= $end)
                fwrite($outFile, $record);
            $b_record_count++;
        } else
            fwrite($outFile, $record);
        $i++;
    }
}

function console($str) {
    echo "<script language='javascript' type='text/javascript'>window.top.window.console_log(\"$str\")</script>";
    echo str_repeat(' ', 1024 * 64);
    flush();
}

?>