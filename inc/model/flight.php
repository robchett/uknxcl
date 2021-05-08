<?php

namespace model;

use classes\ajax;
use classes\attribute_callable;
use classes\db;
use classes\geometry;
use classes\get;
use classes\jquery;
use classes\table;
use Exception;
use html\node;
use JetBrains\PhpStorm\Pure;
use stdClass;
use track\track;


/**
 */
class flight extends table {


    const DOWNLOAD_IGC = 'igc';
    const DOWNLOAD_KML = 'kml';
    const DOWNLOAD_JSON = 'json';
    const DOWNLOAD_KML_SPLIT = 'kml_split';
    const DOWNLOAD_KML_EARTH = 'kml_earth';
    public static array $launch_types = [0 => 'Foot', 1 => 'Aerotow', 2 => 'Winch'];
    public static array $default_joins = ['pilot' => 'flight.pid = pilot.pid', 'glider' => 'flight.gid = glider.gid', 'club' => 'flight.cid = club.cid', 'manufacturer' => 'glider.mid = manufacturer.mid'];
    public static array $default_fields = ['flight.*', 'pilot.name', 'club.title', 'glider.name', 'manufacturer.title AS manufacturer_title'];
    public string $title;
    public $added;
    public $vis_info;
    public $admin_info;
    public $base_score;
    public $cid;
    public $club_name;
    public $coords;
    public $defined;
    public $did;
    /** @var int Flight ID */
    public ?int $fid;
    /** @var string Date flown */
    public string $date;
    public $ftid;
    public $gid;
    public $glider_name;
    public $lid;
    public $manufacturer_title;
    public $multi;
    /** @var string Pilot name */
    public string $p_name;
    /** @var string Club name */
    public string $c_name;
    /** @var string Glider name */
    public string $g_name;
    /** @var string Glider manufacture name */
    public ?string $gm_title;
    /** @var string Coordinates for the flight */
    public string $coordinates;
    /** @var int glider class - 1 or 5 for rigid or flex */
    public int $class;
    /** @var int The id used for a flight, depends on whether $class is 1 or 5 */
    public int $ClassID;
    public $club_title;
    public $winter;
    public $delayed;
    public $pid;
    public $speed;
    public ?track $track = null;
    public $pilot_name;
    public $ridge;
    public $score;
    public $time;
    public $season;
    public $duration;
    public $od_score;
    public $od_time;
    public $od_coordinates;
    public $or_score;
    public $or_time;
    public $or_coordinates;
    public $tr_score;
    public $tr_time;
    public $tr_coordinates;
    public $ft_score;
    public $ft_time;
    public $ft_coordinates;
    public $go_score;
    public $go_time;
    public $go_coordinates;
    public $go_type;
    public $go_distance;
    public $name;
    public $p_pid;
    public int $dim;
    public $club;
    public $glider;
    public $created;
    public $pilot;

    /**
     *
     */
    public static function get_statistics(): array {
        $year_stats = [];

        $flights = new stdClass();
        $flights->min = 0;
        $flights->max = 0;
        $flights->draw_graph = true;
        $flights->colour = get::js_colour(0);
        $flights->name = 'Total Flights';

        $scores = new stdClass();
        $scores->min = 0;
        $scores->max = 0;
        $scores->draw_graph = true;
        $scores->colour = get::js_colour(1);
        $scores->name = 'Total Score';

        foreach (range(1991, date('Y')) as $year) {
            $year_object = new stdClass();
            $year_object->data = [];
            $year_object->min_flights = $year_object->min_score = $year_object->max_score = $year_object->max_flights = 0;
            $year_object->min_score = 0;
            $year_object->draw_graph = true;
            $year_object->colour = get::js_colour($year);
            $year_object->name = $year;

            $total_score = 0;
            $total_flights = 0;

            foreach (range(1, 12) as $month) {
                $score = db::select('flight')->retrieve('sum(score) AS score')->filter(['YEAR(date)=:year', 'MONTH(date)=:month'], ['year' => $year, 'month' => $month])->execute()->fetchObject()->score;
                $tot = db::count('flight', 'fid')->filter(['YEAR(date)=:year', 'MONTH(date)=:month'], ['year' => $year, 'month' => $month])->execute();
                $year_object->data[] = [$month - 1, $tot, $score];
                $year_object->max_flights = max($year_object->max_flights, $tot);
                $year_object->max_score = max($year_object->max_score, $score);
                $total_score += $score;
                $total_flights += $tot;
            }
            $flights->data[] = [$year - 1991, $total_flights * 40];
            $flights->max = max($flights->max, $total_flights);
            $scores->data[] = [$year - 1991, $total_score];
            $scores->max = max($scores->max, $total_score);
            $year_stats[] = $year_object;
        }

        $wrapper = new stdClass();
        $inner = new stdClass();
        $inner->track = $year_stats;
        $inner->xMin = 1;
        $inner->xMax = 12;
        $wrapper->nxcl_data = $inner;

        $return['month'] = $wrapper;

        $wrapper = new stdClass();
        $inner = new stdClass();
        $inner->track = [$flights, $scores];
        $inner->xMin = 1991;
        $inner->xMax = date('Y');
        $wrapper->nxcl_data = $inner;

        $return['year'] = $wrapper;

        return $return;
    }

    /**
     *
     */
    public function download() {
        $id = (int)$_REQUEST['id'];
        $this->do_retrieve(['flight.*', 'pilot.name'], ['join' => ['pilot' => 'flight.pid=pilot.pid'], 'where_equals' => ['flight.fid' => $id]]);
        $temp = isset($_REQUEST['temp']);
        $type = $_REQUEST['type'];
        if ($type == static::DOWNLOAD_KML && isset($_REQUEST['split'])) {
            $type = static::DOWNLOAD_KML_SPLIT;
        }
        if ((isset($this->fid) && $this->fid) || $temp) {
            $fullPath = $this->get_download_path($type, $temp);
            $fsize = filesize($fullPath);
            $pathArray = explode('.', $fullPath);
            $ext = end($pathArray);
            header("Content-type: application/octet-stream");
            header("Cache-control: private");
            header('Content-Disposition: filename="' . $id . '-' . str_replace(' ', '_', $this->pilot->name) . '-' . ($temp ? $id : $this->date) . '.' . $ext . '"');
            header("Content-length: $fsize");
            echo file_get_contents($fullPath);
            die();
        }
    }

    public function get_download_path($type, $tmp = false): string {
        switch ($type) {
            case static::DOWNLOAD_KML:
                $filename = 'track.kml';
                break;
            case static::DOWNLOAD_KML_EARTH:
                $filename = 'track_earth.kml';
                break;
            case static::DOWNLOAD_KML_SPLIT:
                $filename = 'track_split.kml';
                break;
            case static::DOWNLOAD_JSON:
                $filename = 'track.js';
                break;
            case static::DOWNLOAD_IGC:
            default:
                $filename = 'track.igc';
        }
        return root . ($tmp ? '/.cache/' : '/uploads/flight/') . $this->get_primary_key() . '/' . $filename;
    }

    /**
     * @return array
     */
    public function get_best_score(): array {
        $scores = [[$this->od_score * $this->get_multiplier(flight_type::OD_ID, $this->season), flight_type::OD_ID], [$this->or_score * $this->get_multiplier(flight_type::OR_ID, $this->season), flight_type::OR_ID], [$this->tr_score * $this->get_multiplier(flight_type::TR_ID, $this->season), flight_type::TR_ID], [$this->ft_score * $this->get_multiplier(flight_type::FT_ID, $this->season), flight_type::FT_ID],];
        usort($scores, function ($a, $b) {
            return $a[0] - $b[0];
        });
        return end($scores);
    }

    /**
     * @param null $type
     * @param null $season
     * @return int
     */
    public function get_multiplier($type = null, $season = null): int {
        if (!$this->ridge) {
            return flight_type::get_multiplier(isset($type) ? $type : $this->ftid, isset($season) ? $season : $this->season, $this->ridge);
        } else {
            return 1;
        }
    }

    public function get_flights_by_area() {
        $range = db::select('flight')->retrieve(['MAX(date) AS max', 'MIN(date) AS min'])->filter('did > 1 AND (os_codes LIKE "%SU%" OR os_codes LIKE "%TQ%")')->set_limit(1)->execute()->fetch();
        echo print_r($range);
        $flights = flight::get_all([], ['where' => 'did > 1 AND (os_codes LIKE "%SU%" OR os_codes LIKE "%TQ%")', 'order' => 'fid DESC']);
        echo count($flights);
        $flights->iterate(function (flight $flight, $cnt) {
            $track = new track($flight->fid);
            $path = $track->get_kmz_raw();
            if (file_exists($path)) {
                copy($path, root . '/tmp/' . $cnt . '.kmz');
            }
        });
    }

    /**
     *
     */
    public static function get_info_ajax() {
        $html = '';
        $id = (int)$_REQUEST['fid'];
        $flight = new static();
        $flight->do_retrieve(self::$default_fields, ['join' => array_merge(self::$default_joins, ['manufacturer' => 'glider.mid=manufacturer.mid']), 'where_equals' => ['flight.fid' => $id]]);
        if (!isset($flight->fid) || !$flight->fid) {
            $html .= 'Flight not found, this is a bug...';
        } else {
            $html = node::create('table', ['width' => '100%'], node::create('tr', [], "<td>Flight ID</td><td>{$flight->fid}</td>") . node::create('tr', [], "<td>Pilot</td><td>{$flight->pilot->name}</td>") . node::create('tr', [], "<td>Date</td>" . node::create('td', [], date('d/m/Y', $flight->date))) . node::create('tr', [], "<td>Glider</td><td>{$flight->manufacturer_title} - {$flight->glider->name}</td>") . node::create('tr', [], "<td>Club</td><td>{$flight->club->title}</td>") . node::create('tr', [], "<td>Defined</td>" . node::create('td', [], get::bool($flight->defined))) . node::create('tr', [], "<td>Launch</td>" . node::create('td', [], get::launch($flight->lid))) . node::create('tr', [], "<td>Type</td>" . node::create('td', [], get::flight_type($flight->ftid))) . node::create('tr', [], "<td>Ridge Lift</td>" . node::create('td', [], get::bool($flight->ridge))) . node::create('tr', [], "<td>Score</td><td>{$flight->base_score}x{$flight->multi} = {$flight->score}</td>") . node::create('tr', [], "<td>Coordinates</td>" . node::create('td', [], str_replace(';', '; ', $flight->coords))) . node::create('tr', [], "<td>Info</td>" . node::create('td.info', [], $flight->vis_info)) .

                (file_exists(root . '/uploads/flight/' . $flight->fid . '/track.kml') ? node::create('tr td.center.view', ['colspan' => 2], node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flight(' . $flight->fid . ')'], 'Add trace to Map')) . node::create('tr td.center.view', ['colspan' => 2], node::create('a.download.igc', ['href' => $flight->get_download_url(), 'title' => "Download IGC", 'rel' => 'external'], 'Download IGC') . node::create('a.download.kml', ['href' => $flight->get_download_url('kml'), 'title' => 'Download KML', 'rel' => 'external'], 'Download KML')) : node::create('tr td.center.view.coords', ['colspan' => 2], node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flight_coordinates(\'' . $flight->coords . '\',' . $flight->fid . ');return false;'], node::create('span.glyphicon.glyphicon-pushpin', []) . 'Add coordinates to map'))) . node::create('a.close.glyphicon.glyphicon-remove', ['title' => 'close', 'onclick' => '$("#pop").remove()']));
        }

        ajax::add_script('$("#pop").remove();', true);
        ajax::inject('#' . $_REQUEST['origin'], 'after', node::create('div#pop.callout.callout-primary', [], "<span class='arrow'>Arrow</span>" . node::create('div.content', [], $html)));
        ajax::add_script('if($("#pop").offset().left > 400)$("#pop").addClass("reverse");');
    }

    protected function get_download_url($type = 'igc'): string {
        return '/?module=\model\flight&act=download&id=' . $this->fid . '&type=' . $type;
    }

    public function move_temp_files($temp_id) {
        $old_dir = root . '/.cache/' . $temp_id;
        $new_dir = $this->get_file_loc();
        rename($old_dir, $new_dir);
    }

    public function get_file_loc(): string {
        return root . '/uploads/flight/' . $this->fid;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function get_info(): string {
        $coords = $this->coord_info();
        $coords_html = '';
        foreach ($coords as $coord) {
            $coords_html .= "
        <tr>
            <td class='left'>{$coord->lat}</td>
            <td class='left'>{$coord->lng}</td>
            <td class='left'>{$coord->os}</td>
        </tr>";
        }
        $timings = '';
        // $timings = $this->launched ? "
        // <tr>
        //     <td class='left'>Launched@</td>
        //     <td>{ $this->date_format($this->launched, 'H:i:s'}</td>
        // </tr>
        // <tr>
        //     <td class='left'>Landed@</td>
        //     <td>{ $this->date_format($this->landed, 'H:i:s'}</td>
        // </tr>
        // <tr>
        //     <td class='left'>Duration@</td>
        //     <td>{ $this->date_format($this->landed - $this->launched, 'H:i:s'}</td>
        // </tr>" : 0;

        $files = [
            'igc' => $this->has_download(static::DOWNLOAD_IGC) ? $this->get_download_url(static::DOWNLOAD_IGC) : false,
            'kml' => $this->has_download(static::DOWNLOAD_KML) ? $this->get_download_url(static::DOWNLOAD_KML) : false,
        ];

        $view = match ($files['igc'] || $files['kml']) {
            true => "<tr>
            <td class='center view' colspan='2'>
                <a class='button' href='#' onclick='map.add_flight({$this->get_primary_key()})'>Add trace to Map</a>
            </td>
        </tr>
        <tr>
            <td class='center' colspan='2'>
                <a class='download igc' href='{$files['igc']}' rel='external' download>Download IGC</a>
                <a class='download kml' href='{$files['kml']}' rel='external' download>Download KML</a>
            </td>
        </tr>",
            false => "
        <td class='center view coords' colspan='2'>
            <a class='button' href='#' onclick='map.add_flight_coordinates('{$this->coords}',{$this->get_primary_key()});return false;'>Add coordinates to Map</a>
        </td>",
        };

        return "
        <table class='main'>
    <tr>
        <td class='left'>Flight ID</td>
        <td>{$this->get_primary_key()}</td>
    </tr>
    <tr>
        <td class='left'>Glider</td>
        <td>{$this->manufacturer_title} - {$this->glider->name}</td>
    </tr>
    <tr>
        <td class='left'>Club</td>
        <td>{$this->club->name}</td>
    </tr>
    <tr>
        <td class='left'>Defined</td>
        <td>{$this->get_boolean_string($this->defined)}</td>
    </tr>
    <tr>
        <td class='left'>Launch</td>
        <td>{$this->lid}</td>
    </tr>
    <tr>
        <td class='left'>Type</td>
        <td>{$this->ftid}</td>
    </tr>
    <tr>
        <td class='left'>Ridge Lift</td>
        <td>{$this->get_boolean_string($this->ridge)}</td>
    </tr>
    <tr>
        <td class='left'>Score</td>
        <td>{$this->base_score}x{$this->multi} = {$this->score}</td>
    </tr>
    <tr>
        <td class='left'>Coordinates</td>
        <td>
            <table class='main'>
                <thead>
                <tr>
                    <th class='left'>Lat</th>
                    <th class='left'>Lng</th>
                    <th class='left'>OS</th>
                </tr>
                </thead>
                $coords_html
            </table>
        </td>
    </tr>
    $timings
    <tr>
        <td class='left'>Info</td>
        <td>{$this->vis_info}</td>
    </tr>
    $view
</table>
<a class='close' title='close' onclick='$(\"#pop\").remove()'>Close</a>
        ";
    }

    /**
     * @return array
     * @throws Exception
     */
    private function coord_info(): array {
        $env = [];
        $coordinates = explode(';', $this->coords);
        foreach ($coordinates as $coord) {
            $lat_lng = geometry::os_to_lat_long($coord);
            $env[] = ['lat' => round($lat_lng->lat(), 6), 'lng' => round($lat_lng->lng(), 6), 'os' => $coord];
        }
        return $env;
    }

    public function has_download($type): bool {
        return file_exists($this->get_download_path($type, $tmp = false));
    }

    public function get_boolean_string(bool $bool): string {
        return $bool ? 'Yes' : 'No';
    }

    /**
     *
     */
    public function get_js() {
        if (isset($_REQUEST['id'])) {
            $id = (int)$_REQUEST['id'];
            header("Content-type: application/json");
            $root = root . ($id > 100000 ? '/.cache/' : '/uploads/flight/') . $id . '/';
            die(preg_replace('/\s+/im', ' ', file_get_contents($root . 'track.js')));
        }
    }

    /**
     * @param string $prefix
     * @return string
     */
    function to_print($prefix = ''): string {
        if ($this->did == 3) {
            $lead = '';
            $i = 'kml';
        } else if ($this->did == 2) {
            $lead = "2D";
            $i = 'kml';
        } else {
            $lead = 'No Track';
            $i = '';
        }
        if ($this->defined)
            $d = "defined"; else
            $d = "";
        $b = round($this->score, 2);
        $type = get::type($this->ftid);
        return '
<td class="left" style="width: 60px">
    <div class="flash">' . implode(' | ', array_filter([$lead, get::launch_letter($this->lid)])) . '&nbsp;</div>
    <div class="wrap ' . implode(' ', [$type, $d, $i]) . '">
        <a id="fid' . $this->fid . '" class="click' . $this->fid . '" href="' . $this->get_url() . '" data-ajax-click="' . attribute_callable::create([\model\flight::class, 'get_info_ajax']) . '" data-ajax-post=\'' . json_encode(['fid' => $this->fid]) . '\' title=Flight:' . $this->fid . '>' . $prefix . $b . '</a>
   </div>
</td>';
    }

    /**
     * @return string
     */
    public function get_url(): string {
        return '/flight_info/' . $this->fid;
    }

    public function set_date($date) {
        $this->date = $date;
        $this->season = date('Y', $date);
        if (date('m', $date) >= 11) {
            $this->season++;
        }
        $month = date('m', $date);
        $this->winter = ($month == 1 || $month == 2 || $month == 12);
    }

    #[Pure]
    public function get_date_string(string $format = 'Y-m-d'): string {
        return date($format, $this->date);
    }

    public function get_delayed_string(): string {
        return $this->delayed ? 'Yes' : 'No';
    }
}
