<?php
class comp extends table {
    public static $module_id = 17;
    public $table_key = 'cid';
    public $cid;
    public $type;
    public $date;
    public $round;
    public $task;
    public $combined_name;
    public $reverse_pilot_name;
    /** @var coordinate_bound */
    public $bounds;

    const TASK_POINT_ENTRY = 1;
    const TASK_POINT_EXIT = 2;

    const TASK_SPEED_SECTION_OPEN = 1;
    const TASK_SPEED_SECTION_CLOSE = 2;


    /**
     * Gets the url of the object
     * @return string
     */
    public function get_url() {
        return '/comps/' . $this->cid;
    }

    /* @return comp_array */
    public static function get_all(array $fields, array $options = array()) {
        return comp_array::get_all($fields, $options);
    }

    /**
     * @param $p
     * @param $tp
     * @return int
     */
    public function distCalc2($p, $tp) {
        $p_lat = deg2rad($p->lat);
        $p_lon = deg2rad($p->lon);
        $tp_lat = deg2rad($tp->lat);
        $tp_lon = deg2rad($tp->lon);
        $x = (int) (acos(sin($p_lat) * sin($tp_lat) + cos($p_lat) * cos($tp_lat) * cos($p_lon - $tp_lon)) * geometry::EARTH_RADIUS);
        return $x;
    }

    /**
     * @param $point
     * @param $tp
     * @return int
     */
    public function distCalc3($point, $tp) {
        $tp_lat = deg2rad($tp->lat);
        $tp_lon = deg2rad($tp->lon);
        $x = (int) (acos($point->sin_lat * sin($tp_lat) + $point->cos_lat * cos($tp_lat) * cos($point->lonRad - $tp_lon)) * geometry::EARTH_RADIUS);
        return $x;
    }

    /**
     *
     */
    public function do_save() {
        if (isset($this->coords)) {
            if (strstr($this->coords, 'lat:')) {
                $this->set_task_from_fs_comp();
            } else if (strstr($this->coords, ';')) {
                $this->set_task_from_legacy();
            }
        }
        parent::do_save();
    }

    /**
     *
     */
    private function set_task_from_fs_comp() {
        $task = array();
        $matches = array();
        preg_match_all('/.*?m\s(.*?)\s.*?\s(\d*)m.*?lat:(.*?)\s+lon:(.*?)\)/', $this->coords, $matches);
        foreach ($matches[0] as $key => $match) {
            $coord = new stdClass();
            $coord->lat = $matches[3][$key];
            $coord->lon = $matches[4][$key];
            $coord->radius = $matches[2][$key];
            $coord->type = 1;
            if ($matches[1][$key] == 'SS') {
                $coord->speed_section = self::TASK_SPEED_SECTION_OPEN;
            } else if ($matches[1][$key] == 'SS') {
                $coord->speed_section = self::TASK_SPEED_SECTION_CLOSE;
            } else {
                $coord->speed_section = 0;
            }
            $task[] = $coord;
        }
        $this->coords = json_encode($task);
    }

    /**
     *
     */
    private function set_task_from_legacy() {
        $out = explode(';', trim($this->coords, ';'));
        foreach ($out as &$a) {
            $a = explode(',', $a);
        }
        $task = array();

        foreach ($out as &$a) {
            $coord = new stdClass();
            $coord->lat = $a[0];
            $coord->lon = $a[1];
            $coord->radius = $a[2];
            $coord->type = $a[3];
            $task[] = $coord;
        }
        $this->coords = json_encode($task);
    }

    /**
     *
     */
    public function do_zip_to_comp() {
        set_time_limit(0);
        $html = '';
        $root = root . '/uploads/comp/' . $this->cid;
        $this->combined_name = $this->type . ' ' . date('Y', strtotime($this->date)) . ' Round ' . $this->round . ' Task ' . $this->task;

        $kml_out = new kml();
        $kml_out->set_folder_styles();
        $kml_out->get_kml_folder_open($this->combined_name, 1, '', true);

        $kml_earth = new kml();
        $kml_earth->set_folder_styles();
        $kml_earth->get_kml_folder_open($this->combined_name, 1, '', true);

        $js_file = fopen($root . '/points.js', 'w');


        if (is_dir($root . '/tracks')) {
            $tracks = glob($root . '/tracks/*');
            foreach ($tracks as $track) {
                unlink($track);
            }
        }

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
            $track->get_limits();
            $track->name = ucfirst($pilot);
            $track_array[] = $track;
        }
        $this->bounds = new coordinate_bound();
        $track_array->reset_iterator();
        $track_array->uasort(function ($a, $b) {
                return ($a->name < $b->name) ? -1 : 1;
            }
        );

        $startT = 100000000000000000000;
        $endT = 0;
        $cnt = 0;
        //$track_array->iterate(function (track $track, $cnt) use (&$startT, &$endT) {
        foreach ($track_array as $track) {
            /** @var track $track */
            $track->colour = $cnt;
            if ($track->track_points->last()->time > $endT) {
                $endT = $track->track_points->last()->time;
            }
            if ($track->track_points->first()->time < $startT) {
                $startT = $track->track_points->first()->time;
            }
            $this->bounds->add_bounds_to_bound($track->bounds);
            $cnt++;
        } //-1
        //);
        $timeSteps = ($endT - $startT) / 1000;
        $task = $this->output_task();
        $turnpoints = count($task->task_array);
        for ($i = 0; $i < $turnpoints - 1; $i++) {
            $task->distances[0] = 0;
            $task->distances[$i + 1] = $this->distCalc2($task->task_array[$i], $task->task_array[$i + 1]);
        }
        $json_html = '<div class="kmltree new"><ul class="kmltree"><li data-path=\'{"type":"comp","path":[]}\' class="kmltree-item check KmlFolder visible open"><div class="expander"></div><div class="toggler"></div>' . $this->combined_name . '<ul>';
        $js = new stdClass();
        $js->StartT = $startT - mktime(0, 0, 0);
        $js->EndT = $endT - mktime(0, 0, 0);
        $js->Name = $this->title;
        $js->CName = $this->type;
        $js->turnpoints = $turnpoints;
        $js->bounds = $this->bounds->get_js();
        $js->track = array();
        fwrite($js_file, substr(json_encode($js), 0, -2));
        $count = 0;
        //$track_array->iterate(function ($track, $count) use (&$json_html, $kml_out, &$html, $task, $startT, $js_file,$timeSteps) {
        foreach ($track_array as $track) {
            /** @var track $track */
            $track->repair_track();
            $track->get_graph_values();
            $track->get_limits();
            $json_html .= '<li data-path=\'{"type":"comp","path":[' . ($count) . ']}\' class="kmltree-item check KmlFolder hideChildren visible"><div class="expander"></div><div class="toggler"></div><span style="color:#' . substr(get::kml_colour($track->colour), 4, 2) . substr(get::kml_colour($track->colour), 2, 2) . substr(get::kml_colour($track->colour), 0, 2) . '">' . $track->name . '</span></li>';
            $kml_out->add($track->generate_kml_comp());
            $kml_earth->add($track->generate_kml_comp_earth());
            $html .= '<h5>' . $track->name . '</h5>';
            $html .= '<div>';
            if (isset($_REQUEST['add'])) {
                $form = new comp_convert();
                $form->get_field_from_name('file')->value = $track->source;
                $form->get_field_from_name('comp')->value = $track->cid;
                $form->get_field_from_name('vis_info')->value = $this->title . ' - ' . $this->type;
                $html .= $form->get_html();

            }
            $tp = 0;
            $madeTp = 0;
            $dist = 0;
            $distToTP = 120000000000000;
            $cnt = 0;
            foreach ($task->task_array as $turnpoint) {
                $cnt++;
                $tot = count($track->track_points);
                for ($tp; $tp < $tot; $tp++) {
                    if ($turnpoint->type) {
                        $x = ($turnpoint->radius - $this->distCalc3($track->track_points[$tp], $turnpoint));
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
                        $y = ($this->distCalc3($track->track_points[$tp], $turnpoint) - $turnpoint->radius);
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
                if ($turnpoint->type) {
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
            $js_track->min_cr = $track->min_cr;
            $js_track->maximum_cr = $track->maximum_cr;
            $js_track->min_speed = 0;
            $js_track->maximum_speed = $track->maximum_speed;
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
                } else if ($time > $track->track_points->last()->time) {
                    $js_track->coords[] = $track->track_points->last()->get_js_coordinate($track->track_points->last()->time - $startT);
                } else {
                    for ($p = $tp; $p < $track->track_points->count(); $p++) {
                        if (($startT + ($i * $timeSteps)) < $track->track_points[$p]->time) {
                            $js_track->coords[] = $track->track_points[$p]->get_js_coordinate($track->track_points[$p]->time - $startT);
                            $tp = $p;
                            break;
                        }
                    }
                }
            }
            $html .= '<pre>' . $track->log_file . '</pre>';
            $html .= '</div>';
            fwrite($js_file, ($count ? ',' : '') . json_encode($js_track));
            $track->cleanup();
            $count++;
        }
        //);
        $json_html .= '<li data-path=\'{"type":"comp","path":[' . $track_array->count() . ']}\' class="kmltree-item check KmlFolder hideChildren visible"><div class="expander"></div><div class="toggler"></div>Task</li>';
        $json_html .= '</ul></li></ul></div>';
        fwrite($js_file, '], "html":' . json_encode($json_html) . '}');
        $kml_out->add($task->o);
        $kml_out->get_kml_folder_close();
        $kml_out->compile(false, root . '/uploads/comp/' . $this->cid . '/track.kml');
        $kml_earth->add($task->o);
        $kml_earth->get_kml_folder_close();
        $kml_earth->compile(false, root . '/uploads/comp/' . $this->cid . '/track_earth.kml');
        $html .= '</div>';

        if (ajax) {
            jquery::colorbox(array('html' => $html));
        }
    }

    /**
     *
     */
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

    /**
     * @param $coord
     * @return string
     */
    public function get_circle_cords($coord) {
        $out = "";
        $angularDistance = $coord->radius / 6378137;
        $center_lat = deg2rad($coord->lat);
        $center_lon = deg2rad($coord->lon);
        for ($i = 0; $i <= 360; $i++) {
            $bearing = deg2rad($i);
            $lat = Asin(Sin($center_lat) * Cos($angularDistance) + Cos($center_lat) * Sin($angularDistance) * Cos($bearing));

            $dlon = Atan2(Sin($bearing) * Sin($angularDistance) * Cos($center_lat), Cos($angularDistance) - Sin($center_lat) * Sin($lat));

            $lon = fmod(($center_lon + $dlon + M_PI), 2 * M_PI) - M_PI;
            $latOut = rad2deg($lat);
            $lonOut = rad2deg($lon);
            $out .= $lonOut . ',' . $latOut . ',' . 0 . ' ';
        }
        return $out;
    }

    /**
     *
     */
    public function get_js() {
        if (isset($_REQUEST['id'])) {
            $id = (int) $_REQUEST['id'];
            header("Content-type: application/json");
            die(preg_replace('/\s+/im', ' ', file_get_contents(root . '/uploads/comp/' . $id . '/points.js')));
        }
    }

    /**
     * @return stdClass
     */
    public function output_task() {

        $out = new stdClass();
        $out->in = $this->coords;
        $out->o = new kml();;
        $out->o->get_kml_folder_open('Task', 1, 'hideChildren');
        $out->task_array = json_decode($this->coords);
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
                    " . $this->get_circle_cords($matches) . "
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

        foreach ($out->task_array as $coord) {
            $out->o->add($coord->lon . ',' . $coord->lat . ",-100 ");
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

    /**
     * @param field $field
     */
    protected function do_upload_file(field $field) {
        if ($field->field_name == 'file') {
            if (isset($_FILES[$field->field_name]) && !$_FILES[$field->field_name]['error']) {
                $tmp_name = $_FILES[$field->field_name]['tmp_name'];
                $name = $_FILES[$field->field_name]['name'];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                if ($ext == 'zip') {
                    if (!is_dir(root . '/uploads/' . get_class($this) . '/' . $this->{$this->table_key})) {
                        mkdir(root . '/uploads/' . get_class($this) . '/' . $this->{$this->table_key});
                    }
                    move_uploaded_file($tmp_name, root . '/uploads/' . get_class($this) . '/' . $this->{$this->table_key} . '/comp.zip');
                    db::query('UPDATE comp SET file=:file WHERE cid=:cid', array('file' => '/uploads/' . get_class($this) . '/' . $this->{$this->table_key} . '/comp.zip', 'cid' => $this->cid));
                }
            }
        } else {
            parent::do_upload_file($field);
        }
    }
}

class comp_array extends table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, 'comp_iterator');
        $this->iterator = new comp_iterator($input);
    }

    /* @return comp */
    public function next() {
        return parent::next();
    }

    /**
     *
     */
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