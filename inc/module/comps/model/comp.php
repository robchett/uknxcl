<?php

namespace module\comps\model;

use classes\ajax;
use classes\coordinate_bound;
use classes\db;
use classes\geometry;
use classes\lat_lng;
use classes\table;
use classes\interfaces\model_interface;
use form\field_file;
use html\node;
use model\flight;
use model\pilot;
use module\add_flight\form\igc_form;
use module\add_flight\form\igc_upload_form;
use stdClass;
use track\igc_parser;
use ZipArchive;

class comp implements model_interface {
    use table;

    const TASK_POINT_ENTRY = 1;
    const TASK_POINT_EXIT = 2;
    const TASK_SPEED_SECTION_OPEN = 1;
    const TASK_SPEED_SECTION_CLOSE = 2;

    public coordinate_bound $bounds;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $cid,
        public string $type,
        public int $round,
        public int $task,
        public int $date,
        public string $title,
        public string $coords,
        public int $cgid,
        public comp_group $comp_group,
        public bool $reverse_pilot_name,
        public string $file,
    )
    {
    }

    public static function get_js(): void {
        if (isset($_REQUEST['id']) && ($comp = comp::getFromId((int) $_REQUEST['id']))) {
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
     */
    public function get_url(): string {
        return '/comps/' . $this->cid;
    }

    public static function do_save(array $data): int {
        if (isset($data['coords']) && is_string($data['coords'])) {
            $data['coords'] = strip_tags($data['coords']);
            $data['coords'] = preg_replace("/\r/s", "", $data['coords']);
            $data['coords'] = preg_replace("/\n+/s", "\n", $data['coords']);
            $data['coords'] = str_replace("&nbsp;", " ", $data['coords']);
            $data['coords'] = html_entity_decode($data['coords']);
            if (!json_decode($data['coords'])) {
                if (strstr($data['coords'], 'lat:')) {
                    $data['coords'] = self::set_task_from_fs_comp($data['coords']);
                } else if (strstr($data['coords'], ';')) {
                    $data['coords'] = self::set_task_from_legacy($data['coords']);
                } else if ($data['coords'][0] == 1) {
                    $data['coords'] = self::set_task_from_html($data['coords']);
                }
            }
        }

        return table::do_save($data);
    }

    private static function set_task_from_fs_comp(string $coords): string {
        $task = [];
        $matches = [];
        preg_match_all('/.*?m\s(.*?)\s.*?\s(\d*)m.*?lat:(.*?)\s+lon:(.*?)\)/', $coords, $matches);
        foreach ($matches[0] as $key => $_) {
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
        return json_encode($task);
    }

    private static function set_task_from_legacy(string $coords): string {
        $out = explode(';', trim($coords, ';'));
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
        return json_encode($task);
    }

    private static function set_task_from_html(string $coords): string {
        $task = [];
        $matches = [];
        preg_match_all('/([A-Z]*)\s*[\d,]*\skm.*?(\d+)m.*?Lat: ([\d.]*) Lon: ([\-\d.]*)/', $coords, $matches);

        foreach (array_keys($matches[0]) as $key) {
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
        return json_encode($task);
    }

    public static function download(): void {
        $id = (int)$_REQUEST['id'];
        if (!$flight = self::getFromId($id)) {
            return;
        }
        
        header("Content-type: application/octet-stream");
        header("Cache-control: private");
        $fullPath = '';
        if ($flight->cid || isset($_REQUEST['temp'])) {
            if (!isset($_REQUEST['type']) || $_REQUEST['type'] == 'kml') {
                $fullPath = root . '/uploads/comp/' . $id . '/track_earth.kml';
            } else if ($_REQUEST['type'] == 'igc') {
                $fullPath = root . '/uploads/comp/' . $id . '/track.igc';
            } else if ($_REQUEST['type'] == 'kmz') {
                if (file_exists(root . '/uploads/comp/' . (isset($_REQUEST['temp']) ? 'temp/' : '') . $id . '/track.kmz')) {
                    $zip = new ZipArchive();
                    $zip->open(root . '/uploads/comp/' . (isset($_REQUEST['temp']) ? 'temp/' : '') . $id . '/track.kmz');
                    $size = (int) $zip->statName($fullPath)['size'];
                    $file = (string) $zip->statName($fullPath)['name'];
                    $zip->close();
                } else {
                    $file = file_get_contents(root . '/uploads/comp/' . (isset($_REQUEST['temp']) ? 'temp/' : '') . $id . '/comp.kml');
                    $size = mb_strlen($file, "UTF-8");
                }
                header("Content-length: $size");
                header('Content-Disposition: filename="' . $id . (!isset($_REQUEST['temp']) ? '-' . $flight->type . '_' . date('Y', $flight->date) . '_round' . $flight->round . '_task' . $flight->task : '') . '.kml"');
                echo $file;
                return;
            }
            if ($fullPath && $fd = fopen($fullPath, "r")) {
                $fsize = filesize($fullPath);
                header('Content-Disposition: filename="' . $id . '-' . $flight->type . '_' . date('Y' . $flight->date) . '_round' . $flight->round . '_task' . $flight->task . '.' . ((string)$_REQUEST['type']) . '"');
                header("Content-length: $fsize");
                while (!feof($fd)) {
                    $buffer = fread($fd, 2048);
                    echo $buffer;
                }
                fclose($fd);
            }
        }
    }

    public static function add_flight(): void {
        if (!$comp = self::getFromId((int) $_REQUEST['cid'])) {
            return;
        }
        /** @var array{lat: float, lon: float}[] $coords */
        $coords = json_decode($comp->coords, true);
        $form = new igc_upload_form();
        $form->file = (string) $_REQUEST['path'];
        $form->coords = implode(';', array_map(function ($coord) {
            $point = new lat_lng($coord['lat'], $coord['lon']);
            return geometry::lat_long_to_os($point);
        }, $coords));
        $form->do_submit();
        $form = new igc_form();

        $form->vis_info = 'Flown in comp: ' . $comp->type . ' Round ' . $comp->round . ' Task ' . $comp->task;

        $parts = explode(' ', (string) $_REQUEST['name']);
        $pilot = pilot::get(new \classes\tableOptions(where_equals: ['name' => (string) $_REQUEST['name']])) ?: pilot::get(new \classes\tableOptions(where_equals: ['name' => implode(' ', array_reverse($parts))]));
        if ($pilot) {
            $form->pid = $pilot->get_primary_key();
            $flight = flight::get(new \classes\tableOptions(where_equals: ['pid' => $pilot->get_primary_key()], order: 'date DESC'));
            if ($flight) {
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
                $tmp_name = (string) $_FILES[$field->field_name]['tmp_name'];
                $name = (string) $_FILES[$field->field_name]['name'];
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
                    $files = array_map(function (string $file): array {
                        $exp = explode("/", $file);
                        return ['name' => trim(preg_replace('/[^a-zA-Z ]/', '', substr(end($exp), 0, -3))), 'source' => $file];
                    }, $files);
                    if ($files) {
                        /** @var array{lat: float, lon: float}[] */
                        $coords = json_decode($this->coords, true);
                        $parser = new igc_parser();
                        $parser->exec($this->get_primary_key(), ['type' => 'comp', 'sources' => $files, 'destination' => $root, 'task' => ['type' => 'lat/lng', 'coordinates' => array_map(function ($coord) {
                            return ['lat' => $coord['lat'], 'lng' => $coord['lon']];
                        }, $coords)]]);
                    }
                    move_uploaded_file($tmp_name, root . $this->file);
                    db::update('comp')->add_value('file', $this->file)->filter_field('cid', $this->cid)->execute();
                }
            }
            return true;
        }
        return false;
    }
}
