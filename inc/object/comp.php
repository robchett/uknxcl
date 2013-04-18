<?php
class comp extends table {
    public static $module_id = 17;
    public $table_key = 'cid';

    /* @return comp_array */
    public static function get_all(array $fields, array $options = array()) {
        return comp_array::get_all($fields, $options);
    }

    public function distCalc2($p, $tp) {
        $x = (int) (acos(sin($p[0]) * sin($tp[0]) + cos($p[0]) * cos($tp[0]) * cos($p[1] - $tp[1])) * 6371000);
        return $x;
    }

    public function distCalc3($point, $tp) {
        $x = (int) (acos($point->sin_lat * sin($tp[0]) + $point->cos_lat * cos($tp[0]) * cos($point->lonRad - $tp[1])) * 6371000);
        return $x;
    }

    public function do_zip_to_comp() {
        set_time_limit(0);
        $html = '';
        $root = root . '/uploads/comp/' . $this->cid;

        $zip = new ZipArchive;
        if ($zip->open($root . '/comp.zip') === true) {
            $zip->extractTo($root . '/tracks/');
            $zip->close();
        } else {
            die('zip extraction failed');
        }

        $track_array = new table_array();
        $cnt = 0;
        foreach (glob($root . '/tracks/*.igc') as $file) {
            $cnt++;
            $name = explode('/', $file);
            $file_name_data = explode('.', $name[count($name) - 1]);
            $pilot = str_replace('_', ' ', $file_name_data [0]);
            if ($this->reverse_pilot_name) {
                $pilot = explode(' ', $pilot, 4);
                $pilot = $pilot[1] . ' ' . $pilot[0];
            }
            $track = new track();
            $track->source = $file;
            $track->parse_IGC();
            $track->trim();
            $track->repair_track();
            $track->get_limits();
            $track->name = ucfirst($pilot);
            $track_array[] = $track;
        }
        $this->bounds = new coordinate_bound();
        $track_array->reset_iterator();
        $track_array->uasort(function($a, $b) {
            return ($a->name < $b->name) ? -1 : 1;
        });

        $startT = 100000000000000000000;
        $endT = 0;
        $track_array->iterate(function(track $track, $cnt) use (&$startT, &$endT) {
            $track->colour = $cnt;
            if ($track->track_points->last()->time > $endT) {
                $endT = $track->track_points->last()->time;
            }
            if ($track->track_points->first()->time < $startT) {
                $startT = $track->track_points->first()->time;
            }
            $this->bounds->add_bounds_to_bound($track->bounds);
        }, -1);
        $timeSteps = ($endT - $startT) / 1000;
        $task = $this->output_task2();
        $turnpoints = count($task->task_array);
        for ($i = 0; $i < $turnpoints - 1; $i++) {
            $task->distances[0] = 0;
            $task->distances[$i + 1] = $this->distCalc2($task->task_array[$i], $task->task_array[$i + 1]);
        }
        $kml_out = new kml();
        $kml_earth = new kml();
        $kml_out->set_folder_styles();
        $kml_earth->set_folder_styles();
        $this->combined_name = $this->type . ' ' . date('Y', strtotime($this->date)) . ' Round ' . $this->round . ' Task ' . $this->task;
        $kml_out->get_kml_folder_open($this->combined_name, 1, '', true);
        $kml_earth->get_kml_folder_open($this->combined_name, 1, '', true);
        $json_html = '<div class="kmltree new"><ul class="kmltree"><li data-path=\'{"type":"comp","path":[0]}\' class="kmltree-item check KmlFolder visible open"><div class="expander"></div><div class="toggler"></div>' . $this->combined_name . '<ul>';
        $js = new stdClass();
        $js->StartT = $startT - mktime(0, 0, 0);
        $js->EndT = $endT - mktime(0, 0, 0);
        $js->Name = $this->title;
        $js->CName = $this->type;
        $js->turnpoints = $turnpoints;
        $js->track = array();

        $count = 0;

        foreach ($track_array as $track) {
            $html .= '<h5>' . $track->name . '</h5>';
            $html .= '<div>';
            if (isset($_REQUEST['add'])) {
                $form = new comp_convert();
                $form->get_field_from_name('file')->value = $track->source;
                $form->get_field_from_name('comp')->value = $track->cid;
                $form->get_field_from_name('vis_info')->value = $this->title . ' - ' . $this->type;
                $html .= $form->get_html();

            }
            $json_html .= '<li data-path=\'{"type":"comp","path":[0,' .$count . ']}\' class="kmltree-item check KmlFolder hideChildren visible"><div class="expander"></div><div class="toggler"></div><span style="color:#' . substr(get::kml_colour($track->colour),4, 2) . substr(get::kml_colour($track->colour),2,2) . substr(get::kml_colour($track->colour),0, 2) . '">' . $track->name . '</span></li>';
            $kml_out->add($track->generate_kml_comp());
            $kml_earth->add($track->generate_kml_comp_earth());
            $tp = 0;
            $madeTp = 0;
            $dist = 0;
            $distToTP = 120000000000000;
            foreach ($task->task_array as $turnpoint) {
                $tot = count($track->track_points);
                for ($tp; $tp < $tot; $tp++) {
                    if ($turnpoint[3]) {
                        $x = ($turnpoint[2] - $this->distCalc3($track->track_points[$tp], $turnpoint));
                        if ($x > 0) {
                            $html .= 'made turnpoint ' . $madeTp . '<br/>';
                            $dist += $task->distances[$madeTp];
                            $madeTp++;
                            $distToTP = 120000000000000000;
                            continue 2;
                        } else if (-$x < $distToTP) {
                            $distToTP = -$x;
                        }
                    } else {
                        $y = ($this->distCalc3($track->track_points[$tp], $turnpoint) - $turnpoint[2]);
                        if ($y > 0) {
                            $html .= 'made turnpoint ' . $madeTp . '<br/>';
                            $madeTp++;
                            continue 2;
                        } else if (isset($task->task_array[$madeTp + 1])) {
                            $x = ($this->distCalc3($track->track_points[$tp], $task->task_array[$madeTp + 1]) - $task->task_array[$madeTp + 1][2]);
                            if ($x < $distToTP) {
                                $distToTP = $x;
                            }
                        } else if (-$y < $distToTP) {
                            $distToTP = -$y;
                        }
                    }
                }
                if ($turnpoint[3]) {
                    $dist += $task->distances[$madeTp] - $distToTP;
                } else if (!isset($task->task_array[$madeTp + 1])) {
                    $dist += $turnpoint[2] - $distToTP;
                } else {
                    $dist += $task->distances[$madeTp + 1] - $distToTP;
                }
                break;
            }

            $js_track = new stdClass();
            $js_track->pilot = $track->name;
            $js_track->colour = get::js_colour($track->colour);
            $js_track->minEle = $track->min_ele;
            $js_track->maxEle = $track->maximum_ele;
            $js_track->drawGraph = 1;
            $js_track->turnpoint = $madeTp;
            $js_track->score = $dist / 1000;
            $js_track->coords = array();
            $js_track->bounds = $track->bounds->get_js();

            $tp = 0;
            for ($i = 0; $i < 1000; $i++) {
                $time = $startT + ($i * $timeSteps);
                if ($time < $track->track_points->first()->time) {
                    $js_track->coords[] = $track->track_points->first()->get_js_coordinate($track->track_points->first()->time - $startT);
                    continue;
                }
                if ($time > $track->track_points->last()->time) {
                    $js_track->coords[] = $track->track_points->last()->get_js_coordinate($track->track_points->last()->time - $startT);
                    continue;
                }
                for ($p = $tp; $p < $track->track_points->count(); $p++) {
                    if (($startT + ($i * $timeSteps)) < $track->track_points[$p]->time) {
                        $js_track->coords[] = $track->track_points[$p]->get_js_coordinate($track->track_points[$p]->time - $startT);
                        $tp = $p;
                        break;
                    }
                }
            }
            $js->track[] = $js_track;
            $count++;
            $html .= '<pre>' . $track->log_file . '</pre>';
            $html .= '</div>';
        }
        $json_html .= '</ul></li></ul></div>';
        $js->html = $json_html;
        $js->bounds = $this->bounds->get_js();
        file_put_contents($root . '/points.js', json_encode($js));
        $kml_out->add($task->o);
        $kml_out->get_kml_folder_close();
        $kml_out->compile(false, 'uploads/comp/' . $this->cid . '/track.kml');
        $kml_earth->add($task->o);
        $kml_earth->get_kml_folder_close();
        $kml_earth->compile(false, 'uploads/comp/' . $this->cid . '/track_earth.kml');
        $html .= '</div>';

        if (ajax) {
            jquery::colorbox(array('html' => $html));
        }
    }

    public function generate_kml() {
        if (isset($_POST['id'])) {
            $this->do_retrieve_from_id(array(), $_POST['id']);
            if ($this->cid) {
                $this->do_zip_to_comp();
            }
        }
    }


    /*
     * @return track
     * */

    public function get_circle_cords2($cords) {
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

    public function get_js() {
        if (isset($_REQUEST['id'])) {
            $id = (int) $_REQUEST['id'];
            header("Content-type: application/json");
            die(preg_replace('/\s+/im', ' ', file_get_contents(root . 'uploads/comp/' . $id . '/points.js')));
        }
    }

    public function output_task2() {

        $out = new stdClass();
        $out->in = $this->coords;
        $out->o = new kml();
        ;
        $out->o->get_kml_folder_open('Task', 1, 'hideChildren');
        $out->task_array = explode(';', $this->coords);
        foreach ($out->task_array as &$a) {
            $a = explode(',', $a);
        }
        unset($out->task_array[sizeof($out->task_array) - 1]);
        foreach ($out->task_array as &$a) {
            $a[0] = deg2rad($a[0]);
            $a[1] = deg2rad($a[1]);
        }
        foreach ($out->task_array as $matches) {
            $out->o->add("<Placemark>
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
                    " . $this->get_circle_cords2($matches) . "
                    </coordinates>
                </LinearRing>
            </outerBoundaryIs>
        </Polygon>
    </Placemark>"
            );
        }
        $out->o->add("<Placemark>
    <LineString>
    <altitudeMode>clampToGround</altitudeMode>
        <coordinates>"
        );

        foreach ($out->task_array as $cords) {
            $lon = rad2deg($cords [0]);
            $lat = rad2deg($cords[1]);
            $out->o->add($lat . ',' . $lon . ",-100 ");
        }
        $out->o->add("</coordinates>
    </LineString>
    </Placemark>
    "
        );
        $out->task = $this->coords;
        $out->o->get_kml_folder_close();
        $out->o = $out->o->compile(true);
        return $out;
    }

    protected function do_upload_file(field $field) {
        if ($field->field_name == 'file') {
            if (isset($_FILES[$field->field_name]) && !$_FILES[$field->field_name]['error']) {
                $tmp_name = $_FILES[$field->field_name]['tmp_name'];
                $name = $_FILES[$field->field_name]['name'];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                if ($ext == 'zip') {
                    if (!is_dir(root . 'uploads/' . get_class($this) . '/' . $this->{$this->table_key})) {
                        mkdir(root . 'uploads/' . get_class($this) . '/' . $this->{$this->table_key});
                    }
                    move_uploaded_file($tmp_name, root . 'uploads/' . get_class($this) . '/' . $this->{$this->table_key} . '/comp.zip');
                    db::query('UPDATE comp SET file=:file WHERE cid=:cid', array('file' => '/uploads/' . get_class($this) . '/' . $this->{$this->table_key} . '/comp.zip', 'cid' => $this->cid));
                }
            }
        } else {
            parent::do_upload_file($field);
        }
    }

    protected function get_circle_cords($wpt) {
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


}

class comp_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'comp_iterator');
        $this->iterator = new comp_iterator($input);
    }

    /* @return comp */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class comp_iterator extends table_iterator {

    /* @return comp */
    public function key() {
        return parent::key();
    }
}