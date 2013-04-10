<?php

class comp_maker extends core_module {

    public function __controller(array $path) {
        $this->view = 'default';
        core::$css = array('/inc/module/comp_maker/css/comp_maker.css');
        parent::__controller($path);
    }

    public function get() {
        return '
<h2>Competition Creator</h2>
<form target="upload_target" method="post" enctype="multipart/form-data"
              action="/index.php?module=comp_maker&act=do_zip_to_comp">
    <input type="file" name="file" id="file"/>Choose a .zip file containing the .igc files from the task.<br/>
    <input type="text" name="name"/>Name for the task.<br/>
    <input type="text" name=\'Cname\'/>Name for the comp.<br/>
    <input type="text" name="start"/>First start time (leave blank to calculate automatically with an error caused
    by extra gps reading.<br/>
    <input type="text" name="end"/>Last landing ^ see above.<br/>
    <textarea cols="31" rows="3" name="task"></textarea>Task details from fs comp.<br/>
    <input type="checkbox" name="add"/>Add flights to UKNXCL. (will check pilot names against date to make sure that
    none are duplicated).<br/>
    <input type="checkbox" name="fs"/>If the name format of the .igc files is forename surname check this box.<br/>
    <input type="submit" name="submit"/>
</form>
<div id="comp_maker_target"></div>
        ';
    }

    public function do_zip_to_comp() {
        echo "<div id='result'>";
        set_time_limit(0);
        if (!isset($_REQUEST['id'])) {
            $result = db::query("SELECT cpid FROM Comps ORDER BY cpid DESC LIMIT 1");
            $t = db::fetch($result);
            $id = $t->cpid + 1;
            $dir = root . '/uploads/comp/' . $id . '/';
            // check task is valid
            if (isset($_REQUEST['task']) && $_REQUEST['task'] != '') {
                $task = outputTask($_REQUEST['task']);
                $task = outputTask2($task);
            } else {
                $task = new stdClass();
                $task->task_array = array();
                $task->o = '';
                $task->in = 'No Task';
            }
            $find_start = (isset($_REQUEST['start']) && $_REQUEST['start'] != '') ? false : true;
            $find_end = (isset($_REQUEST['end']) && $_REQUEST['end'] != '') ? false : true;
            if (!file_exists($dir)) mkdir($dir);
            else {
                foreach (glob($dir . 'tracks/*', GLOB_MARK) as $file) {
                    unlink($file);
                }
                rmdir($dir . 'tracks');
                foreach (glob($dir . '*', GLOB_MARK) as $file) {
                    unlink($file);
                }
            }
            move_uploaded_file($_FILES ["file"] ["tmp_name"], $dir . 'comp.zip');
            $update_sql = true;
        } else {
            $id = $_REQUEST['id'];
            $result = execute("SELECT * FROM Comps WHERE cpid=$id LIMIT 1");
            $t = mysql_fetch_assoc($result);
            $_REQUEST['start'] = $t->StartTime;
            $find_start = false;
            $_REQUEST['end'] = $t->EndTime;
            $find_end = false;
            $_REQUEST['Cname'] = $t->Name;
            $_REQUEST['name'] = $t->TaskName;
            $_REQUEST['task'] = $t->Cords;
            $task = outputTask2($_REQUEST['task']);
            $update_sql = false;
            $find_start = (isset($_REQUEST['getTimes'])) ? true : false;
            $find_end = (isset($_REQUEST['getTimes'])) ? true : false;
        }
// create the files to store data
        $js_out = fopen($dir . 'points.js', 'w');
        $kml_out = fopen($dir . 'track.kml', 'w');

        $zip = new ZipArchive;
        if ($zip->open($dir . 'comp.zip') === TRUE) {
            $zip->extractTo($dir . 'tracks/');
            $zip->close();
        } else {
            die('zip extraction failed');
        }

        $fileArray = array();
        foreach (glob($dir . 'tracks/*igc') as $file) {
            $name = explode('/', $file);
            $file_name_data = explode('.', $name[count($name) - 1]);
            $pilot = str_replace('_', ' ', $file_name_data [0]);
            if (isset($_REQUEST['fs'])) {
                $pilot = explode(' ', $pilot, 4);
                $pilot = "$pilot[1] $pilot[0]";
            }
            // Store all in an array
            $fileArray [] = array($file, $pilot);

        }
        usort($fileArray, 'cmp');

        if ($find_start) $startT = 100000000000000000000; else $startT = strtotime($_REQUEST['start']);
        if ($find_end) $endT = 0; else $endT = strtotime($_REQUEST['end']);
        if ($find_end || $find_start) {
            foreach ($fileArray as &$file) {
                $track = new track();
                $track->source = $file[0];
                $track->parse_IGC();
                $track->trim();
                if ($find_end && $track->track_points->last()->time > $endT) {
                    $endT = $track->track_points->last()->time;
                }
                if ($find_start && $track->track_points->first()->time < $startT) {
                    $startT = $track->track_points->first()->time;
                }
                $file[] = $track;
            }
        }
        unset($file);
        $timeSteps = ($endT - $startT) / 1000;
        $turnpoints = count($task->task_array);
        for ($i = 0; $i < $turnpoints - 1; $i++) {
            $task->distances[0] = 0;
            $task->distances[$i + 1] = distCalc2($task->task_array[$i], $task->task_array[$i + 1]);
        }
        fwrite($kml_out, kml::get_kml_header());
        $kml_earth = new kml();

        $js = new stdClass();
        $js->StartT = $startT - mktime(0, 0, 0);
        $js->EndT = $endT - mktime(0, 0, 0);
        $js->Name = $_REQUEST['name'];
        $js->CName = $_REQUEST['Cname'];
        $js->turnpoints = $turnpoints;
        $js->track = array();

        $count = 0;

        foreach ($fileArray as $file) {
            echo '<h5>' . $file[1] . '</h5>';
            echo '<div>';
            $out = convert_by_id_comp($file, $count, $startT, $endT);

            if (isset($_REQUEST['add'])) {
                $form = new comp_convert();
                $form->get_field_from_name('file')->value = $file[0];
                $form->get_field_from_name('pilot')->options = alphabeticalise::pilot_array(substr($file[1], 0, 5));
                $form->get_field_from_name('glider')->options = alphabeticalise::glider_array();
                $form->get_field_from_name('club')->options = alphabeticalise::club_array();
                $form->get_field_from_name('comp')->value = $id;
                $form->get_field_from_name('vis_info')->value = $_REQUEST['Cname'] . '' . $_REQUEST['name'];
                echo $form->get_html();

            }
            fwrite($kml_out, $out->output);
            $kml_earth->add(output_earth);
            // javascript output per pilot
            $tp = 0;
            $madeTp = 0;
            $dist = 0;
            $distToTP = 120000000000000;
            foreach ($task->task_array as $turnpoint) {
                for ($tp; $tp < $out->ntrack_points; $tp++) {
                    if ($turnpoint[3]) {
                        $x = ($turnpoint[2] - distCalc3($out->track_points[$tp], $turnpoint));
                        if ($x > 0) {
                            echo"made turnpoint $madeTp<br/>";
                            $dist += $task->distances[$madeTp];
                            $madeTp++;
                            $distToTP = 120000000000000000;
                            continue 2;
                        } elseif (-$x < $distToTP) $distToTP = -$x;
                    } else {
                        $y = (distCalc3($out->track_points[$tp], $turnpoint) - $turnpoint[2]);
                        if ($y > 0) {
                            echo"made turnpoint $madeTp<br/>";
                            $madeTp++;
                            continue 2;
                        } elseif (isset($task->task_array[$madeTp + 1])) {
                            $x = (distCalc3($out->track_points[$tp], $task->task_array[$madeTp + 1]) - $task->task_array[$madeTp + 1][2]);
                            if ($x < $distToTP) $distToTP = $x;
                        } elseif (-$y < $distToTP) $distToTP = -$y;
                    }
                }
                if ($turnpoint[3]) {
                    $dist += $task->distances[$madeTp] - $distToTP;
                } elseif (!isset($task->task_array[$madeTp + 1])) $dist += $turnpoint[2] - $distToTP; else $dist += $task->distances[$madeTp + 1] - $distToTP;
                break;
            }

            $js_track = new stdClass();
            $js_track->pilot = $out->name;
            $js_track->colour = get::js_colour($out->colour);
            $js_track->minEle = $out->min_ele;
            $js_track->maxEle = $out->maximum_ele;
            $js_track->drawGraph = 1;
            $js_track->turnpoint = $madeTp;
            $js_track->score = $dist / 1000;
            $js_track->coords = array();

            $tp = 0;
            for ($i = 0; $i < 1000; $i++) {
                $time = $startT + ($i * $timeSteps);
                if ($time < $out->track_points->first()->time) {
                    $js_track->coords[] = $out->track_points->first()
                        ->get_js_coordinate($out->track_points->first()->time - $startT);
                    continue;
                }
                if ($time > $out->track_points->last()->time) {
                    $js_track->coords[] = $out->track_points->last()
                        ->get_js_coordinate($out->track_points->last()->time - $startT);
                    continue;
                }
                for ($p = $tp; $p < $out->track_points->count(); $p++) {
                    if (($startT + ($i * $timeSteps)) < $out->track_points[$p]->time) {
                        $js_track->coords[] = $out->track_points[$p]
                            ->get_js_coordinate($out->track_points[$p]->time - $startT);
                        $tp = $p;
                        break;
                    }
                }
            }
            $js->track[] = $js_track;
            $count++;
            echo '<pre>' . $out->log_file . '</pre>';
            echo '</div>';
        }
        fwrite($js_out, 'var out = ' . json_encode($js));
        fwrite($kml_out, $task->o . kml::get_kml_footer());
        $kml_earth_out = fopen($dir . 'track_earth.kml', 'w');
        fwrite($kml_earth_out, $kml_earth->compile());
        echo "
</div>
<script language='javascript' type='text/javascript'>
    window.top.document.getElementById('comp_maker_target').innerHTML = document.getElementById('result').innerHTML;
    window.top.window.map.add_comp($id);
</script>";
        if ($update_sql) {
            db::query('INSERT INTO `comps` SET
                cpid=:cpid,
                start_time=:start,
                end_time=:END,
                `NAME`=:NAME,
                `TYPE`=:TYPE,
                cords=:cords,
                task_name=:task_name',
                array('cpid' => $id,
                    'start' => date('H:i:s', $startT),
                    'end' => date('H:i:s', $endT),
                    'name' => $_REQUEST['Cname'],
                    'type' => 0,
                    'cords' => $task->in,
                    'task_name' => $_REQUEST['name']
                )
            );
        }
    }
}

/*
 * @return track
 * */
function convert_by_id_comp($file, $count, $start, $end) {
    if (!isset($file[2])) {
        $track = new track();
        $track->source = $file;
        $track->parse_IGC();
    } else {
        $track = $file[2];
    }
    $track->repair_track();
    $track->get_limits();
    $track->colour = $count;
    $track->name = $file[1];
    $track->output = $track->generate_kml_comp();
    $track->output_earth = $track->generate_kml_comp_earth();
    ;
    return $track;
}

function outputTask($file) {
    $out = "";
    preg_match_all('/.*\(([0-9]+)\s+lat:\s?([0-9.-]+)\s+lon:\s?([0-9.-]+).*/i', $file, $matches);
    if (($size = sizeof($matches [1])) == 0) {
        echo 'no valid task - please copy directly from fs comp';
    } else {
        for ($i = 0; $i < $size; $i++) {
            if ($i == 1) $out .= "{$matches[2][$i]},{$matches[3][$i]},{$matches[1][$i]},0;";
            else     $out .= "{$matches[2][$i]},{$matches[3][$i]},{$matches[1][$i]},1;";
        }
        echo $out;
        return $out;
    }
}

function getCircleCords($wpt) {
    $out = "";
    $cords = explode(',', $wpt);
    $latRad = deg2rad($cords [0]);
    $lonRad = deg2rad($cords [1]);
    $angularDistance = $cords [2] / 6378137;
    for ($i = 0; $i <= 360; $i++) {
        $bearing = deg2rad($i);
        $lat = Asin(Sin($latRad) * Cos($angularDistance) + Cos($latRad) * Sin($angularDistance) * Cos($bearing));

        $dlon = Atan2(Sin($bearing) * Sin($angularDistance) * Cos($latRad), Cos($angularDistance) - Sin($latRad) * Sin($lat));

        $lon = fmod(($lonRad + $dlon + M_PI), 2 * M_PI) - M_PI;
        $latOut = rad2deg($lat);
        $lonOut = rad2deg($lon);
        $out .= "$lonOut,$latOut,0 ";
    }
    return $out;
}

function outputTask2($task) {
    $out = new stdClass();
    $out->in = $task;
    $out->o = "";
    $out->task_array = explode(';', $task);
    foreach ($out->task_array as &$a) {
        $a = explode(',', $a);
    }
    unset($out->task_array[sizeof($out->task_array) - 1]);
    foreach ($out->task_array as &$a) {
        $a[0] = deg2rad($a[0]);
        $a[1] = deg2rad($a[1]);
    }
    foreach ($out->task_array as $matches) {
        $out->o .= "<Placemark>
        <altitudeMode>clampToGround</altitudeMode>
        <Style>
	        <PolyStyle>
			  <color>55ffffaa</color>
			  <fill>1</fill>
			  <outline>1</outline>
			</PolyStyle>
		</Style>
        <Polygon>
            <tessellate>1</tessellate>
            <outerBoundaryIs>
                <LinearRing>
                    <coordinates>
                    " . getCircleCords2($matches) . "
                    </coordinates>
                </LinearRing>
            </outerBoundaryIs>
        </Polygon>
    </Placemark>";
    }
    $out->o .= "<Placemark>
    <LineString>
    <altitudeMode>clampToGround</altitudeMode>
        <coordinates>";

    foreach ($out->task_array as $cords) {
        $lon = rad2deg($cords [0]);
        $lat = rad2deg($cords[1]);
        $out->o .= $lat . ',' . $lon . ",-100 ";
    }
    $out->o .= "</coordinates>
    </LineString>
    </Placemark>
    ";
    $out->task = $task;
    return $out;
}


function getCircleCords2($cords) {
    $out = "";
    $angularDistance = $cords [2] / 6378137;
    for ($i = 0; $i <= 360; $i++) {
        $bearing = deg2rad($i);
        $lat = Asin(Sin($cords [0]) * Cos($angularDistance) + Cos($cords [0]) * Sin($angularDistance) * Cos($bearing));

        $dlon = Atan2(Sin($bearing) * Sin($angularDistance) * Cos($cords [0]), Cos($angularDistance) - Sin($cords [0]) * Sin($lat));

        $lon = fmod(($cords [1] + $dlon + M_PI), 2 * M_PI) - M_PI;
        $latOut = rad2deg($lat);
        $lonOut = rad2deg($lon);
        $out .= "$lonOut,$latOut,0 ";
    }
    return $out;
}

function cmp($a, $b) {
    if ($a [1] == $b [1]) {
        return 0;
    }
    return ($a [1] < $b [1]) ? -1 : 1;
}

function distCalc2($p, $tp) {
    $x = (int) (acos(sin($p[0]) * sin($tp[0]) + cos($p[0]) * cos($tp[0]) * cos($p[1] - $tp[1])) * 6371000);
    return $x;
}

function distCalc3($point, $tp) {
    $x = (int) (acos($point->sin_lat * sin($tp[0]) + $point->cos_lat * cos($tp[0]) * cos($point->lonRad - $tp[1])) * 6371000);
    return $x;
}

