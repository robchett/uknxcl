<?php

namespace module\comps\model;

use classes\ajax;
use classes\coordinate_bound;
use classes\db;
use classes\geometry;
use classes\lat_lng;
use classes\table;
use form\field_file;
use html\node;
use JetBrains\PhpStorm\Pure;
use model\flight;
use model\pilot;
use module\add_flight\form\igc_form;
use module\add_flight\form\igc_upload_form;
use stdClass;
use track\igc_parser;
use ZipArchive;

class comp extends table {


    const TASK_POINT_ENTRY = 1;
    const TASK_POINT_EXIT = 2;
    const TASK_SPEED_SECTION_OPEN = 1;
    const TASK_SPEED_SECTION_CLOSE = 2;
    public $class;
    public $coords;
    public $cid;
    public string $title;
    public $type;
    public $date;
    public $round;
    public $task;
    public $file;
    public $combined_name;
    public $reverse_pilot_name;
    /** @var coordinate_bound */
    public coordinate_bound $bounds;
    public $comp_group;

    /**
     *
     */
    public static function get_js() {
        if (isset($_REQUEST['id'])) {
            $comp = new comp();
            $comp->set_primary_key($_REQUEST['id']);
            header("Content-type: application/json");
            die(preg_replace('/\s+/im', ' ', file_get_contents($comp->get_js_file())));
        }
    }

    public function get_js_file(): string {
        $old = root . '/uploads/comp/' . $this->get_primary_key() . '/points.js';
        $new = root . '/uploads/comp/' . $this->get_primary_key() . '/comp.js';
        if (file_exists($old)) {
            rename($old, $new);
        }
        return $new;
    }

    /**
     * Gets the url of the object
     * @return string
     */
    public function get_url(): string {
        return '/comps/' . $this->cid;
    }

    /**
     *
     */
    public function do_save(): bool {
        if (isset($this->coords)) {
            $this->coords = strip_tags($this->coords);
            $this->coords = preg_replace("/\r/s", "", $this->coords);
            $this->coords = preg_replace("/\n+/s", "\n", $this->coords);
            $this->coords = str_replace("&nbsp;", " ", $this->coords);
            $this->coords = html_entity_decode($this->coords);
            if (!json_decode($this->coords)) {
                if (strstr($this->coords, 'lat:')) {
                    $this->set_task_from_fs_comp();
                } else if (strstr($this->coords, ';')) {
                    $this->set_task_from_legacy();
                } else if ($this->coords[0] == 1) {
                    $this->set_task_from_html();
                }
            }
        }

        return parent::do_save();
    }

    /**
     *
     */
    private function set_task_from_fs_comp() {
        $task = [];
        $matches = [];
        preg_match_all('/.*?m\s(.*?)\s.*?\s(\d*)m.*?lat:(.*?)\s+lon:(.*?)\)/', $this->coords, $matches);
        foreach ($matches[0] as $key => $match) {
            $coord = new stdClass();
            $coord->lat = $matches[3][$key];
            $coord->lon = $matches[4][$key];
            $coord->radius = $matches[2][$key];
            $coord->type = 1;
            if ($matches[1][$key] == 'SS') {
                $coord->speed_section = self::TASK_SPEED_SECTION_OPEN;
            } else if ($matches[1][$key] == 'ES') {
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
        $task = [];

        foreach ($out as $a) {
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
    private function set_task_from_html() {
        $task = [];
        $matches = [];
        preg_match_all('/([A-Z]*)\s*[\d,]*\skm.*?(\d+)m.*?Lat: ([\d.]*) Lon: ([\-\d.]*)/', $this->coords, $matches);

        foreach ($matches[0] as $key => $match) {
            $coord = new stdClass();
            $coord->lat = $matches[3][$key];
            $coord->lon = $matches[4][$key];
            $coord->radius = $matches[2][$key];
            $coord->type = 1;
            if ($matches[1][$key] == 'SS') {
                $coord->speed_section = self::TASK_SPEED_SECTION_OPEN;
            } else if ($matches[1][$key] == 'ES') {
                $coord->speed_section = self::TASK_SPEED_SECTION_CLOSE;
            } else {
                $coord->speed_section = 0;
            }
            $task[] = $coord;
        }
        $this->coords = json_encode($task);
    }

    /**
     * @param $coord
     * @return string
     */
    #[Pure]
    public function get_circle_cords($coord): string {
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
    public function download() {
        $id = (int)$_REQUEST['id'];
        $this->do_retrieve_from_id(['type', 'round', 'task', 'date', 'cid'], $id);
        header("Content-type: application/octet-stream");
        header("Cache-control: private");
        $fullPath = '';
        if ((isset($this->cid) && $this->cid) || isset($_REQUEST['temp'])) {
            if (!isset($_REQUEST['type']) || $_REQUEST['type'] == 'kml') {
                $fullPath = root . '/uploads/comp/' . $id . '/track_earth.kml';
            } else if ($_REQUEST['type'] == 'igc') {
                $fullPath = root . '/uploads/comp/' . $id . '/track.igc';
            } else if ($_REQUEST['type'] == 'kmz') {
                if (file_exists(root . '/uploads/comp/' . (isset($_REQUEST['temp']) ? 'temp/' : '') . $id . '/track.kmz')) {
                    $zip = new ZipArchive();
                    $zip->open(root . '/uploads/comp/' . (isset($_REQUEST['temp']) ? 'temp/' : '') . $id . '/track.kmz');
                    $size = $zip->statName($fullPath)['size'];
                    $file = $zip->statName($fullPath)['name'];
                    $zip->close();
                } else {
                    $file = file_get_contents(root . '/uploads/comp/' . (isset($_REQUEST['temp']) ? 'temp/' : '') . $id . '/comp.kml');
                    $size = mb_strlen($file, "UTF-8");
                }
                header("Content-length: $size");
                header('Content-Disposition: filename="' . $id . (!isset($_REQUEST['temp']) ? '-' . $this->type . '_' . date('Y', $this->date) . '_round' . $this->round . '_task' . $this->task : '') . '.kml"');
                echo $file;
                return;
            }
            if ($fullPath && $fd = fopen($fullPath, "r")) {
                $fsize = filesize($fullPath);
                header('Content-Disposition: filename="' . $id . '-' . $this->type . '_' . date('Y' . $this->date) . '_round' . $this->round . '_task' . $this->task . '.' . $_REQUEST['type'] . '"');
                header("Content-length: $fsize");
                while (!feof($fd)) {
                    $buffer = fread($fd, 2048);
                    echo $buffer;
                }
                fclose($fd);
            }
        }
    }

    public function add_flight() {
        $this->do_retrieve_from_id([], $_REQUEST['cid']);
        $coords = json_decode($this->coords);
        $form = new igc_upload_form();
        $form->file = $_REQUEST['path'];
        $form->coords = implode(';', array_map(function ($coord) {
            $point = new lat_lng($coord->lat, $coord->lon);
            return geometry::lat_long_to_os($point);
        }, $coords));
        $form->do_submit();
        $form = new igc_form();

        $form->vis_info = 'Flown in comp: ' . $this->type . ' Round ' . $this->round . ' Task ' . $this->task;

        $pilot = new pilot();
        $parts = explode(' ', $_REQUEST['name']);
        if ($pilot->do_retrieve([], ['where_equals' => ['name' => $_REQUEST['name']]]) || $pilot->do_retrieve([], ['where_equals' => ['name' => implode(' ', array_reverse($parts))]])) {
            $form->pid = $pilot->get_primary_key();
            $flight = new flight();
            if ($flight->do_retrieve([], ['where_equals' => ['pid' => $pilot->get_primary_key()], 'order' => 'date DESC'])) {
                $form->gid = $flight->gid;
                $form->cid = $flight->cid;
            }
        }
        ajax::update(node::create('div#second_form', [], $form->get_html()));
    }

    /**
     * @param field_file $field
     * @return bool|string
     */
    protected function do_upload_file(field_file $field): bool|string {
        if ($field->field_name == 'file') {
            if (isset($_FILES[$field->field_name]) && !$_FILES[$field->field_name]['error']) {
                $tmp_name = $_FILES[$field->field_name]['tmp_name'];
                $name = $_FILES[$field->field_name]['name'];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                if ($ext == 'zip') {
                    $root = root . '/uploads/comp/' . $this->get_primary_key();
                    if (!is_dir($root)) {
                        mkdir($root);
                    }
                    $this->file = str_replace(root, '', $root) . '/comp.zip';
                    $zip = new ZipArchive();
                    $zip->open(root . $this->file);
                    $zip->extractTo($root . '/');
                    $files = glob($root . '/*.igc');
                    $files = array_map(function ($file) {
                        $exp = explode("/", $file);
                        return ['name' => trim(preg_replace('/[^a-zA-Z ]/', '', substr(end($exp), 0, -3))), 'source' => $file];
                    }, $files);
                    if ($files) {
                        $coords = json_decode($this->coords);
                        $parser = new igc_parser();
                        $parser->exec($this->get_primary_key(), ['type' => 'comp', 'sources' => $files, 'destination' => $root, 'task' => ['type' => 'lat/lng', 'coordinates' => array_map(function ($coord) {
                            return ['lat' => (float)$coord->lat, 'lng' => (float)$coord->lon];
                        }, $coords)]]);
                    }
                    move_uploaded_file($tmp_name, root . $this->file);
                    db::update('comp')->add_value('file', $this->file)->filter_field('cid', $this->cid)->execute();
                }
            }
            return true;
        } else {
            return parent::do_upload_file($field);
        }
    }
}
