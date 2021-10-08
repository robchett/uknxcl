<?php

namespace model;

use classes\ajax;
use classes\attribute_callable;
use classes\db;
use classes\geometry;
use classes\get;
use classes\interfaces\model_interface;
use classes\table;
use classes\tableOptions;
use Exception;
use html\node;
use stdClass;

/**
 * @psalm-consistent-constructor
 */
class flight implements model_interface {
    use table;

    const DOWNLOAD_IGC = 'igc';
    const DOWNLOAD_KML = 'kml';
    const DOWNLOAD_JSON = 'json';
    const DOWNLOAD_KML_SPLIT = 'kml_split';
    const DOWNLOAD_KML_EARTH = 'kml_earth';
    public static array $launch_types = [0 => 'Foot', 1 => 'Aerotow', 2 => 'Winch'];

    public float $speed;
    public float $ft_score;

    /**
     * @param 1|5 $class
     * @param int The id used for a flight, depends on whether class is 1 or 5
     */
    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $fid,
        public int $pid,
        public pilot $pilot,
        public int $cid,
        public club $club,
        public int $gid,
        public glider $glider,
        public int $date,
        public int $did,
        public dimension $dimension,
        public bool $winter,
        public string $vis_info,
        public string $admin_info,
        public int $ftid,
        public flight_type $flight_type,
        public int $lid,
        public launch_type $launch_type,
        public float $multi,
        public float $score,
        public float $base_score,
        public string $coords,
        public bool $personal,
        public bool $ridge,
        public bool $delayed,
        public bool $defined,
        public int $season,
        public string $file,
        public int $duration,
        public float $od_score,
        public int $od_time,
        public string $od_coordinates,
        public float $or_score,
        public int $or_time,
        public string $or_coordinates,
        public float $tr_score,
        public int $tr_time,
        public string $tr_coordinates,
        // public int $ft_time,
        // public string $ft_coordinates,
        public float $go_score,
        public int $go_time,
        // public string $go_coordinates,
        // public int $go_type,
        // public float $go_distance,
        public string $os_codes,
    )
    {
    }

    public static function get_statistics(): array {
        $year_stats = [];

        $flights = new stdClass();
        $flights->min = 0;
        $flights->max = 0;
        $flights->draw_graph = true;
        $flights->colour = get::js_colour(0);
        $flights->name = 'Total Flights';
        $flights->data = [];

        $scores = new stdClass();
        $scores->min = 0;
        $scores->max = 0;
        $scores->draw_graph = true;
        $scores->colour = get::js_colour(1);
        $scores->name = 'Total Score';
        $scores->data = [];

        foreach (range(1991, (int) date('Y')) as $year) {
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
                $score = (float) db::select('flight')->retrieve('sum(score) AS score')->filter(['YEAR(date)=:year', 'MONTH(date)=:month'], ['year' => $year, 'month' => $month])->execute()->fetchObject()->score;
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

        $return = [];
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

    public static function download(): void {
        $id = (int)$_REQUEST['id'];
        $flight = self::get(new \classes\tableOptions(where_equals: ['flight.fid' => $id]));
        $temp = isset($_REQUEST['temp']);
        $type = (string) $_REQUEST['type'];
        if ($type == self::DOWNLOAD_KML && isset($_REQUEST['split'])) {
            $type = self::DOWNLOAD_KML_SPLIT;
        }
        if (($flight && $flight->fid || $temp)) {
            $fullPath = self::_get_download_path($id, $type, $temp);
            $fsize = filesize($fullPath);
            $pathArray = explode('.', $fullPath);
            $ext = end($pathArray);
            header("Content-type: application/octet-stream");
            header("Cache-control: private");
            $name = $flight ? $flight->pilot->name . '-' . $flight->date : ''; 
            header('Content-Disposition: filename="' . $id . '-' .  $name . '.' . $ext . '"');
            header("Content-length: $fsize");
            echo file_get_contents($fullPath);
            die();
        }
    }

    private static function _get_download_path(int $id, string $type, bool $tmp): string {
        $filename = match ($type) {
            static::DOWNLOAD_KML => 'track.kml',
            static::DOWNLOAD_KML_EARTH => 'track_earth.kml',
            static::DOWNLOAD_KML_SPLIT => 'track_split.kml',
            static::DOWNLOAD_JSON => 'track.js',
            default => 'track.igc',
        };
        return root . ($tmp ? '/.cache/' : '/uploads/flight/') . $id . '/' . $filename;
    }

    public function get_download_path(string $type, bool $tmp = false): string {
        return static::_get_download_path($this->get_primary_key(), $type, false);
    }

    public function get_best_score(): array {
        $scores = [
            [$this->od_score * $this->get_multiplier(flight_type::OD_ID, $this->season), flight_type::OD_ID],
            [$this->or_score * $this->get_multiplier(flight_type::OR_ID, $this->season), flight_type::OR_ID],
            [$this->tr_score * $this->get_multiplier(flight_type::TR_ID, $this->season), flight_type::TR_ID],
            [$this->ft_score * $this->get_multiplier(flight_type::FT_ID, $this->season), flight_type::FT_ID],
        ];
        usort($scores, function (array $a, array $b) {
            return $a[0] <=> $b[0];
        });
        return end($scores);
    }

    public function get_multiplier(int $type = null, int $season = null): float {
        if (!$this->ridge) {
            return flight_type::get_multiplier($type ?? $this->ftid, $season ?? $this->season, $this->ridge);
        } else {
            return 1;
        }
    }

    public static function get_info_ajax(): void {
        $html = '';
        $id = (int)$_REQUEST['fid'];
        $flight = self::get(new \classes\tableOptions(where_equals: ['flight.fid' => $id]));
        if (!$flight) {
            $html .= 'Flight not found, this is a bug...';
        } else {
            $html = "
            <table width='100%'>
                <tr><td>Flight ID</td><td>{$flight->fid}</td></tr>
                <tr><td>Pilot</td><td>{$flight->pilot->name}</td></tr>
                <tr><td>Date</td><td>" . date('d/m/Y', $flight->date) . "</td></tr>
                <tr><td>Glider</td><td>{$flight->glider->manufacturer->title} - {$flight->glider->name}</td></tr>
                <tr><td>Club</td><td>{$flight->club->title}</td></tr>
                <tr><td>Defined</td><td>" . get::bool($flight->defined) . "</td></tr>
                <tr><td>Launch</td><td>" . (get::launch($flight->lid) ?: '') . "</td></tr>
                <tr><td>Type</td><td>" . (get::flight_type($flight->ftid) ?: '') . "</td></tr>
                <tr><td>Ridge Lift</td><td>" . get::bool($flight->ridge) . "</td></tr>
                <tr><td>Score</td><td>{$flight->base_score}x{$flight->multi} = {$flight->score}</td></tr>
                <tr><td>Coordinates</td><td>" . str_replace(';', '; ', $flight->coords) . "</td></tr>
                <tr><td>Info</td><td class='info'>" . $flight->vis_info . "</td></tr>
                " . (file_exists(root . '/uploads/flight/' . $flight->fid . '/track.kml')
                    ?
                    node::create('tr td.center.view', ['colspan' => 2], node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flight(' . $flight->fid . ')'], 'Add trace to Map')) .
                    node::create('tr td.center.view', ['colspan' => 2], node::create('a.download.igc', ['href' => $flight->get_download_url(), 'title' => "Download IGC", 'rel' => 'external'], 'Download IGC') . node::create('a.download.kml', ['href' => $flight->get_download_url('kml'), 'title' => 'Download KML', 'rel' => 'external'], 'Download KML'))
                    :
                    node::create('tr td.center.view.coords', ['colspan' => 2], node::create('a.button', ['href' => '#', 'onclick' => 'map.add_flight_coordinates(\'' . $flight->coords . '\',' . $flight->fid . ');return false;'], node::create('span.glyphicon.glyphicon-pushpin', []) . 'Add coordinates to map'))) . "
                <a class='close glyphicon glyphicon-remove' title='close' onclick='$(\"#pop\").remove()'></a>
            </table>";
        }

        ajax::add_script('$("#pop").remove();', true);
        ajax::inject('#' . (string) ($_REQUEST['origin'] ?? ''), 'after', node::create('div#pop.callout.callout-primary', [], "<span class='arrow'>Arrow</span>" . node::create('div.content', [], $html)));
        ajax::add_script('if($("#pop").offset().left > 400)$("#pop").addClass("reverse");');
    }

    protected function get_download_url(string $type = 'igc'): string {
        return '/?module=\model\flight&act=download&id=' . $this->fid . '&type=' . $type;
    }

    public static function move_temp_files(int $new_id, int $temp_id): void {
        $old_dir = root . '/.cache/' . $temp_id;
        $new_dir =  root . '/uploads/flight/' . $new_id;
        rename($old_dir, $new_dir);
    }

    /**
     * @throws Exception
     */
    public function get_info(): string {
        $coords = $this->coord_info();
        $coords_html = '';
        foreach ($coords as $coord) {
            $coords_html .= "
        <tr>
            <td class='left'>{$coord['lat']}</td>
            <td class='left'>{$coord['lng']}</td>
            <td class='left'>{$coord['os']}</td>
        </tr>";
        }
        $files = [
            'igc' => $this->has_download(self::DOWNLOAD_IGC) ? $this->get_download_url(self::DOWNLOAD_IGC) : false,
            'kml' => $this->has_download(self::DOWNLOAD_KML) ? $this->get_download_url(self::DOWNLOAD_KML) : false,
        ];

        /** @psalm-suppress TypeDoesNotContainType */
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
        <td>{$this->glider->manufacturer->title} - {$this->glider->name}</td>
    </tr>
    <tr>
        <td class='left'>Club</td>
        <td>{$this->club->title}</td>
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
     * @return list<array{lat: float, lng: float, os: string}>
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

    public function has_download(string $type): bool {
        return file_exists($this->get_download_path($type));
    }

    public function get_boolean_string(bool $bool): string {
        return $bool ? 'Yes' : 'No';
    }
    public static function get_js(): void {
        if (isset($_REQUEST['id'])) {
            $id = (int)$_REQUEST['id'];
            header("Content-type: application/json");
            $root = root . ($id > 100000 ? '/.cache/' : '/uploads/flight/') . $id . '/';
            die(preg_replace('/\s+/im', ' ', file_get_contents($root . 'track.js')));
        }
    }

    function to_print(string $prefix = ''): string {
        if ($this->did == 3) {
            $lead = '';
            $i = 'kml';
        } elseif ($this->did == 2) {
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
        <a id="fid' . $this->fid . '" class="click' . $this->fid . '" href="' . $this->get_url() . '" data-ajaxclick="' . attribute_callable::create([flight::class, 'get_info_ajax']) . '" data-ajaxpost=\'' . json_encode(['fid' => $this->fid]) . '\' title=Flight:' . $this->fid . '>' . $prefix . $b . '</a>
   </div>
</td>';
    }

    public function get_url(): string {
        return '/flight_info/' . $this->fid;
    }

    public function get_date_string(string $format = 'Y-m-d'): string {
        return date($format, $this->date);
    }

    public function get_delayed_string(): string {
        return $this->delayed ? 'Yes' : 'No';
    }
}
