<?php

namespace module\tables\model;

use classes\get;
use classes\interfaces\model_interface;
use classes\table;
use classes\table_array;
use classes\tableOptions;
use html\node;
use JetBrains\PhpStorm\NoReturn;
use model\club;
use model\flight;
use model\flight_type;
use model\glider;
use model\launch_type;
use model\manufacturer;
use model\pilot;
use model\scorable;
use stdClass;

class league_table
{

    const PRESET_Main = '0';
    const PRESET_Class1 = '14';
    const PRESET_Class5 = '13';
    const PRESET_Foot = '1';
    const PRESET_Aero = '2';
    const PRESET_Winch = '3';
    const PRESET_Defined = '5';
    const PRESET_Winter = '4';
    const PRESET_Female = '6';
    const PRESET_Club = '8';
    const PRESET_ClubOfficial = '7';
    const PRESET_TopTens = '9';
    const PRESET_TopTensNoMultiplier = '15';
    const PRESET_Pilot = '10';
    const PRESET_Hangies = '12';
    const PRESET_Records = '16';
    const PRESET_Dales = '17';

    const LAYOUT_LEAGUE = 0;
    const LAYOUT_CLUB = 1;
    const LAYOUT_PILOT_LOG = 2;
    const LAYOUT_TOP_TEN = 3;
    const LAYOUT_LIST = 4;
    const LAYOUT_RECORDS = 5;

    public bool $show_multipliers = false;
    public string $ScoreType = 'score';
    public string $OrderBy = 'score';
    public string $Title = '';
    public string $year_title = 'All Time';
    public string $class_table_alias = 'p';
    public int $max_flights = 6;
    public string $WHERE = '';
    public string $base_url = '/tables';
    private string $modifier_string = '';
    private result $result;

    /**
     * @param list<int> $launches
     * @param list<int> $types
     * @param list<string> $flown_through
     * @param list<string> $launch_areas
     */
    public function __construct(
        public bool $official = true,
        public int $pilot_id = -1,
        public int $club_id = -1,
        public array $launches = [1, 2, 3],
        public array $types = [1, 2, 3, 4, 5],
        public bool $winter = false,
        public bool $defined = false,
        public int $gender = -1,
        public bool $show_top_4 = false,
        public int $dimensions = -1,
        public bool $ridge = false,
        public bool $no_multipliers = false,
        public string $year = '',
        public int $glider_class = -1,
        public int $layout = 0,
        public bool $hangies = false,
        public bool $split_classes = false,
        public bool $glider_mode = false,
        public int $minimum_score = 10,
        public ?string $date = null,
        public array $flown_through = [],
        public array $launch_areas = [],
        public bool $handicap = false,
        public float $handicap_kingpost = 1,
        public float $handicap_rigid = 1,
    ) {
        $this->year = date('Y');
    }

    public static function decode_url(string $url): array
    {
        $object = [];
        $decode = urldecode($url);
        $decode_parts = explode(',', $decode);
        foreach ($decode_parts as $part) {
            [$key, $val] = [null, null] + explode('~', $part, 2);
            if (!is_null($key) && !is_null($val)) {
                /** @var string $val */
                if (str_starts_with($val, '[')) {
                    $val = explode('|', substr($val, 1, strlen($val) - 2));
                }
                $object[$key] = $val;
            }
        }
        return $object;
    }

    public static function cmp(scorable|club|manufacturer $a, scorable|club|manufacturer $b): int
    {
        if ($a->score == $b->score) {
            return 0;
        }
        return ($a->score > $b->score) ? -1 : 1;
    }

    public function get_show_all_url(): string
    {
        return str_replace('/tables/', '/mass_overlay/', $this->get_url());
    }

    public function get_url(): string
    {
        $ignore_fields = [
            'parent',
            'layout',
        ];
        $url_parts = [];
        $default = new self();
        /** @psalm-suppress MixedAssignment */
        foreach ((array) $this as $option => $value) {
            if (!in_array($option, $ignore_fields) && ((!isset($default->$option) && !is_null($value)) || $default->$option != $value)) {
                $url_parts[$option] = $value;
            }
        }
        return $this->base_url . '/' . $this->get_layout_url() . self::encode_url($url_parts);
    }

    public function get_layout_url(): string
    {
        switch ($this->layout) {
            case league_table::LAYOUT_LEAGUE:
                return '';
            case league_table::LAYOUT_CLUB:
                return 'club/';
            case league_table::LAYOUT_PILOT_LOG:
                return 'pilot_log/';
            case league_table::LAYOUT_TOP_TEN:
                return 'top_ten/';
            case league_table::LAYOUT_LIST:
                return 'list/';
            case league_table::LAYOUT_RECORDS:
                return 'records/';
        }
        return '';
    }

    public static function encode_url(array $parts): string
    {
        /** @psalm-suppress MixedAssignment */
        foreach ($parts as &$part) {
            if (is_array($part)) {
                $part = str_replace([',', '"'], ['|', ''], json_encode($part));
            }
        }
        $json = str_replace([':', '"'], ['~', ''], trim(json_encode($parts), '{}'));
        return urlencode($json);
    }

    public function get_primary_key(): bool|string
    {
        return hash('md5', $this->get_url());
    }

    public function set_from_request(): void
    {
        if (isset($_REQUEST['year']) && $_REQUEST['year'] != '') {
            $this->year = (string) $_REQUEST['year'];
        } else {
            $this->year = 'all_time';
            $this->Title = 'All Time';
        }
        if (isset($_REQUEST['Flights'])) {
            $this->max_flights = (int) $_REQUEST['Flights'];
        }
        if (isset($_REQUEST['layout'])) {
            $this->layout = (int) $_REQUEST['layout'];
        }
        if (isset($_REQUEST['pages'])) {
            $this->official = true;
            $this->Title .= ' Official';
        }
        if (isset($_REQUEST['base'])) {
            $this->ScoreType = 'base_score';
        }
        if (isset($_REQUEST['pilot'], $_REQUEST['layout']) && $_REQUEST['layout'] == 2) {
            $this->pilot_id = (int) $_REQUEST['pilot'];
        }
        if (isset($_REQUEST['handicap_kingpost']) && is_numeric($_REQUEST['handicap_kingpost'])) {
            $this->handicap_kingpost = (float) $_REQUEST['handicap_kingpost'];
        }
        if (isset($_REQUEST['handicap_rigid']) && is_numeric($_REQUEST['handicap_rigid'])) {
            $this->handicap_rigid = (float) $_REQUEST['handicap_rigid'];
        }
        if (isset($_REQUEST['minimum_score'])) {
            $this->minimum_score = (int) $_REQUEST['minimum_score'];
        }
        if (isset($_REQUEST['official'])) {
            $this->official = true;
        }
        if (isset($_REQUEST['league']) && $_REQUEST['league'] == 'hangies') {
            $this->hangies = true;
            $this->minimum_score = 0;
        }
        if (isset($_REQUEST['glider_class'])) {
            $this->glider_class = (int) $_REQUEST['glider_class'];
            if ($_REQUEST['glider_class'] != -1) {
                $this->Title .= ' Class ' . ((int)$_REQUEST['glider_class']);
            }
        }
        if (isset($_REQUEST['winter'])) {
            $this->winter = (bool) $_REQUEST['winter'];
        }
        if (isset($_REQUEST['defined'])) {
            $this->defined = (bool) $_REQUEST['defined'];
        }
        if (isset($_REQUEST['gender'])) {
            $this->gender = (int) $_REQUEST['gender'];
        }
        if (isset($_REQUEST['dimensions'])) {
            $this->dimensions = (int) $_REQUEST['dimensions'];
        }
        if (isset($_REQUEST['ridge'])) {
            $this->ridge = (bool) $_REQUEST['ridge'];
        }
        if (isset($_REQUEST['Date'])) {
            $this->set_date((string) $_REQUEST['Date']);
        }
        if (isset($_REQUEST['no_multipliers'])) {
            $this->no_multipliers = true;
        }
        // Removing launch and flight type from sql.
        if (isset($_REQUEST['launches']) && is_array($_REQUEST['launches'])) {
            $launches = [];
            if (in_array(launch_type::WINCH, $_REQUEST['launches'])) {
                $launches[] = launch_type::WINCH;
            }
            if (in_array(launch_type::FOOT, $_REQUEST['launches'])) {
                $launches[] = launch_type::FOOT;
            }
            if (in_array(launch_type::AERO, $_REQUEST['launches'])) {
                $launches[] = launch_type::AERO;
            }
            $this->launches = $launches;
        }
        if (isset($_REQUEST['types']) && is_array($_REQUEST['types'])) {
            $flight_type = [];
            if (in_array(flight_type::OD_ID, $_REQUEST['types'])) {
                $flight_type[] = flight_type::OD_ID;
            }
            if (in_array(flight_type::OR_ID, $_REQUEST['types'])) {
                $flight_type[] = flight_type::OR_ID;
            }
            if (in_array(flight_type::GO_ID, $_REQUEST['types'])) {
                $flight_type[] = flight_type::GO_ID;
            }
            if (in_array(flight_type::TR_ID, $_REQUEST['types'])) {
                $flight_type[] = flight_type::TR_ID;
            }
            if (in_array(flight_type::FT_ID, $_REQUEST['types'])) {
                $flight_type[] = flight_type::FT_ID;
            }
            $this->types = $flight_type;
        }

        // Choose Classes (glider/pilot), and append official if needed  - default pilot
        if (isset($_REQUEST['object']) && $_REQUEST['object'] == 'Glider') {
            $this->glider_mode = true;
        }

        if (isset($_REQUEST['flown_through'])) {
            $this->set_flown_through((string) $_REQUEST['flown_through']);
        }

        $this->show_top_4 = isset($_REQUEST['show_top_4']);
        $this->split_classes = isset($_REQUEST['split_classes']);
        $this->handicap = isset($_REQUEST['handicap']);
    }

    public function use_preset(string $type, string $year): void
    {
        switch ($type) {
            case (self::PRESET_Main):
                $this->official = true;
                if ($year > 2012) {
                    $this->dimensions = 1;
                }
                break;
            case (self::PRESET_Foot):
                $this->launches = [launch_type::FOOT];
                break;
            case (self::PRESET_Aero):
                $this->launches = [launch_type::AERO];
                break;
            case (self::PRESET_Winch):
                $this->launches = [launch_type::WINCH];
                break;
            case (self::PRESET_Winter):
                $this->winter = true;
                break;
            case (self::PRESET_Defined):
                $this->defined = true;
                break;
            case (self::PRESET_Female):
                $this->gender = 2;
                break;
            case (self::PRESET_ClubOfficial):
                $this->layout = league_table::LAYOUT_CLUB;
                break;
            case (self::PRESET_Club):
                $this->official = false;
                $this->layout = league_table::LAYOUT_CLUB;
                break;
            case (self::PRESET_TopTens):
                $this->layout = league_table::LAYOUT_TOP_TEN;
                break;
            case (self::PRESET_Pilot):
                $this->layout = league_table::LAYOUT_PILOT_LOG;
                break;
            case (self::PRESET_Hangies):
                $this->hangies = true;
                break;
            case (self::PRESET_Class5):
                $this->glider_class = 5;
                $this->official = true;
                break;
            case (self::PRESET_Class1):
                $this->glider_class = 1;
                $this->official = true;
                break;
            case (self::PRESET_TopTensNoMultiplier):
                $this->layout = league_table::LAYOUT_TOP_TEN;
                $this->no_multipliers = true;
                break;
            case (self::PRESET_Records):
                $this->layout = league_table::LAYOUT_RECORDS;
                break;
            case (self::PRESET_Dales):
                $this->club_id = 31;
                $this->set_launched_from('SD,SE,NY');
                break;
        }
    }

    /** 
     * @psalm-suppress InvalidReturnStatement, MoreSpecificReturnType
     * @return array{string[], array<string, scalar>}
     **/
    public function get_sql(): array
    {
        $where = [];
        $params = [];
        $addOptions = function (array $arr) use (&$where, &$params): void {
            /** @psalm-suppress all */
            $where = array_merge($where, $arr[0]);
            /** @psalm-suppress all */
            $params = array_merge($params, $arr[1]);
        };
        $this->get_multipliers();
        $this->get_glider_mode();

        if ($this->hangies) {
            /** @psalm-suppress all */
            $where[] = 'hangies = 1';
        }

        $addOptions($this->get_winter());
        $addOptions($this->get_defined());
        $addOptions($this->get_gender());
        $addOptions($this->get_dimensions());
        $addOptions($this->get_ridge());
        $addOptions($this->get_date());
        $addOptions($this->get_launch_string());
        $addOptions($this->get_flight_type_string());
        $addOptions($this->get_year());
        $addOptions($this->get_class());
        $addOptions($this->get_pilot_id());
        $addOptions($this->get_club_id());
        $addOptions($this->get_flown_through());
        $addOptions($this->get_launched_from());
        $addOptions($this->get_minimum_score());
        /** @psalm-suppress all */
        return [$where, $params];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_winter(): array
    {
        if (!$this->winter) {
            return [[], []];
        }
        return [['winter=:winter'], ['winter' => $this->winter]];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_defined(): array
    {
        if (!$this->defined) {
            return [[], []];
        }
        return [['`defined` = :def'], ['def' => $this->defined]];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_gender(): array
    {
        if ($this->gender == -1) {
            return [[], []];
        }
        return [['flight__pilot.gid = :gender'], ['gender' => $this->gender]];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_dimensions(): array
    {
        if ($this->dimensions == -1 || $this->dimensions == 0) {
            return [[], []];
        }
        if ($this->dimensions == 1) {
            return [['flight.did > 1'], []];
        }
        return [['flight.did = :did'], ['did' => $this->dimensions]];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_ridge(): array
    {
        if (!$this->ridge) {
            return [[], []];
        }
        return [['flight.ridge=:ridge'], ['ridge' => $this->ridge]];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_date(): array
    {
        if (is_null($this->date)) {
            return [[], []];
        }
        return [['date=:date'], ['date' => $this->date]];
    }

    public function set_date(string $value): void
    {
        $time = @strtotime($value) ?: $value;
        if (is_numeric($time) && $time) {
            $this->date = $value;
        }
    }

    public function get_multipliers(): void
    {
        if ($this->no_multipliers) {
            $this->ScoreType = "base_score";
            $this->OrderBy = "base_score";
        }
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_launch_string(): array
    {
        if (!count($this->launches) == 3 || !$this->launches) {
            return [[], []];
        }
        return [['flight.lid IN (' . implode(',', $this->launches) . ')'], []];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_flight_type_string(): array
    {
        if (count($this->types) == 5) {
            return [[], []];
        }
        return [['flight.ftid IN (' . implode(',', $this->types) . ')'], []];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_year(): array
    {
        if ($this->year == 'all_time') {
            return [[], []];
        }
        $param_count = 0;
        $parts = [];
        $str_parts = [];

        $params = [];
        $groups = explode(',', $this->year);
        foreach ($groups as $group) {
            $c = explode('-', $group);
            if (count($c) > 1 && count($c) < 3) {
                $parts[] = '(season BETWEEN :year' . $param_count . ' AND :year' . ($param_count + 1) . ')';
                $str_parts[] = $c[0] . '-' . $c[1];
                $params['year' . ($param_count++)] = $c[0];
                $params['year' . ($param_count++)] = $c[1];
            } else {
                $parts[] = 'season=:year' . $param_count;
                $str_parts[] = $group;
                $params['year' . ($param_count++)] = $group;
            }
        }
        /** @psalm-suppress all */
        if (!$parts) {
            return [[], []];
        }
        $this->year_title = implode(',', $str_parts);
        return [['(' . implode('OR', $parts) . ')'], $params];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_class(): array
    {
        if ($this->glider_class == -1) {
            return [[], []];
        }
        return [['flight__glider.class = :glider_class'], ['glider_class' => $this->glider_class]];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_pilot_id(): array
    {
        if ($this->pilot_id == -1) {
            return [[], []];
        }
        return [['flight__pilot.pid=:pid'], ['pid' => $this->pilot_id]];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_club_id(): array
    {
        if ($this->club_id == -1) {
            return [[], []];
        }
        return [['flight__club.cid=:cid'], ['cid' => $this->club_id]];
    }

    public function get_glider_mode(): void
    {
        if ($this->glider_mode) {
            $this->class_table_alias = 'g';
        }
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_flown_through(): array
    {
        if (!$this->flown_through) {
            return [[], []];
        }
        $sql = [];
        foreach ($this->flown_through as $grid) {
            if (ctype_alpha($grid)) {
                $sql[] = 'os_codes LIKE "%' . $grid . '%" OR coords LIKE "%' . $grid . '%"';
            }
        }
        return $sql ? [['(' . implode(' OR ', $sql) . ')'], []] : [[], []];
    }

    public function set_flown_through(string $grid_refs): array
    {
        $parts = explode(',', $grid_refs);
        $out = [];
        foreach ($parts as $ref) {
            if (strlen($ref) == 2) {
                $out[] = strtoupper($ref);
            }
        }
        return $out;
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_launched_from(): array
    {
        if (!$this->launch_areas) {
            return [[], []];
        }
        $sql = [];
        foreach ($this->launch_areas as $grid) {
            if (ctype_alpha($grid)) {
                $sql[] = 'os_codes LIKE "%' . $grid . '%"';
            }
        }
        return $sql ? [['(' . implode(' OR ', $sql) . ')'], []] : [[], []];
    }

    /** 
     * @return array{string[], array<string, scalar>}
     **/
    public function get_minimum_score(): array
    {
        if (!$this->minimum_score) {
            return [[], []];
        }
        return [[$this->ScoreType . ' > ' . $this->minimum_score], []];
    }

    public function set_launched_from(string $grid_refs): array
    {
        $parts = explode(',', $grid_refs);
        $out = [];
        foreach ($parts as $ref) {
            if (strlen($ref) == 2) {
                $out[] = strtoupper($ref);
            }
        }
        return $out;
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function fromUrl(string $url, array $options): static
    {
        $options['layout'] = match ($url) {
            '' => league_table::LAYOUT_LEAGUE,
            'club' => league_table::LAYOUT_CLUB,
            'pilot_log' => league_table::LAYOUT_PILOT_LOG,
            'top_ten' => league_table::LAYOUT_TOP_TEN,
            'records' => league_table::LAYOUT_RECORDS,
            'list' => league_table::LAYOUT_LIST,
        };

        /** @psalm-suppress all */
        return new static(...$options);
    }

    /**
     * @no-return
     */
    public function generate_csv(): void
    {
        $flights = flight::get_all(new tableOptions(where: '`delayed`=0 AND personal=0 AND score>10 AND season = 2012'));
        /** @var table_array<scorable> */
        $array = new table_array();
        foreach ($flights as $flight) {
            if (!isset($array[$flight->pilot->pid])) {
                $array[$flight->pilot->pid] = $this->getScorable($flight);
                $array[$flight->pilot->pid]->set_from_flight($flight, 6, false);
                $array[$flight->pilot->pid]->output_function = 'csv';
            }
            $array[$flight->pilot->pid]->add_flight($flight, $this->official);
        }
        $array->uasort([self::class, 'cmp']);
        $class1 = $class5 = 1;
        $csv = "Pos ,Name ,Glider ,Club ,Best ,Second ,Third ,Forth ,Fifth ,Sixth ,Total\n";
        foreach ($array as $pilot) {
            if ($pilot->class == 1) {
                $class1++;
                $csv .= $pilot->output($this, $class1);
            } else {
                $class5++;
                $csv .= $pilot->output($this, $class5);
            }
        }
        echo node::create('pre', [], $csv);
        die();
    }

    public function get_table(): string
    {
        if ($this->pilot_id !== -1) {
            $this->layout = league_table::LAYOUT_PILOT_LOG;
        }
        switch ($this->layout) {
            case (league_table::LAYOUT_LEAGUE):
                $this->result = new result_league();
                $this->Title .= ' League';
                break;
            case (league_table::LAYOUT_CLUB):
                $this->result = new result_club();
                $this->Title .= ' Club League';
                break;
            case (league_table::LAYOUT_PILOT_LOG):
                $this->result = new result_pilot();
                if ($pilot = pilot::getFromId($this->pilot_id)) {
                    /** @psalm-suppress all */
                    $this->Title .= ' Pilot Log (' . $pilot->name . ')';
                } else {
                    $this->Title .= ' Pilot not found';
                }
                $this->minimum_score = 0;
                break;
            case (league_table::LAYOUT_TOP_TEN):
                $this->result = new result_top_ten();
                $this->Title .= ' Top 10s';
                break;
            case (league_table::LAYOUT_RECORDS):
                $this->result = new result_records();
                return $this->result->make_table($this);
            case (league_table::LAYOUT_LIST):
                $this->OrderBy = 'date';
                $this->result = new result_list();
                $this->Title .= ' List';
                break;
        }
        return $this->result->make_table($this);
    }

    public function set_modifier_string(): void
    {
        $this->modifier_string = $this->ScoreType . ($this->handicap ? ' * IF(g.kingpost,' . $this->handicap_kingpost . ',1) * IF(g.class = 5,' . $this->handicap_rigid . ',1) ' : '');
    }

    function write_table_header(int $flights, string $type = 'p'): string
    {
        $inner_html = '';
        foreach (range(1, $flights) as $pos) {
            $inner_html .= node::create('th.left', [], get::ordinal($pos));
        }
        return "
        <thead>
            <tr>
                <th class='pos left'>Pos</th>
                <th class='name left'>Name</th>
                <th class='club left'>" . ($type == 'p' ? 'Club / Glider' : 'Manufacturer') . "</th>
                $inner_html
                <th class='tot left'>Total</th>
            </tr>
        </thead>";
    }

    /**
     * @return table_array<flight>
     */
    public function get_flights(): table_array
    {
        [$where, $parameters] = $this->get_sql();
        $this->set_modifier_string();
        $where[] = '`delayed`=0';
        $where = implode(' AND ', $where);
        if (isset($this->type) && $this->type == league_table::LAYOUT_PILOT_LOG) {
            $where .= ' AND p.pid=' . ($this->pilot_id);
            $this->OrderBy = "date";
        } else {
            $where .= " AND personal=0 ";
        }

        return flight::get_all(new tableOptions(
            where: $where,
            order: $this->OrderBy . ' DESC',
            parameters: $parameters,
        ));
    }

    /**
     * Generates a sub table showing the top flights in each category.
     *
     * @param $where - a complete sql WHERE clause
     * @param array $params
     */
    public function ShowTop4(): string
    {
        [$where, $params] = $this->get_sql();
        $html = '';
        $launch_by_number = [1 => 'Foot', 2 => 'Aerotow', 3 => 'Winch'];
        $where['delayed'] = '`delayed`=:delayed';
        $params['delayed'] = 0;
        $where['personal'] = 'personal=:personal';
        $params['personal'] = 0;

        foreach ($launch_by_number as $j => $val) {
            $where_extend = implode(' AND ', $where);
            $where_extend .= ' AND lid=:lid';
            $params['lid'] = $j;
            $html .= '<tr><td>' . $val . '</td>';
            for ($i = 1; $i < 5; $i++) {
                $where_extend .= ' AND flight.ftid=:ftid';
                $params['ftid'] = $i;
                $flight = flight::get(
                    new \classes\tableOptions(
                        where: $where_extend,
                        order: 'score DESC',
                        parameters: $params,
                    )
                );
                if ($flight) {
                    $prefix = $flight->pilot->name . ' (' . ($flight->glider->class == 5 ? 'R' : 'F') . ') ';
                    $html .= $flight->to_print($prefix);
                } else {
                    $html .= "<td>No Flights Fit<td>";
                }
            }
            $html .= '</tr>';
        }

        return "
        <table class='top4 main results'>
            <thead>
                <tr>
                    <th style='width:52px'>Category</th>
                    <th style='width:155px;color:black'>Open Dist</th>
                    <th style='width:155px;color:green'>Out & Return</th>
                    <th style='width:155px;color:red'>Goal</th>
                    <th style='width:155px;color:blue'>Triangle</th>
                </tr>
            </thead>
        </table>" . $html;
    }

    public function getScorable(flight $f): scorable
    {
        return match ($this->class_table_alias) {
            'p' => $f->pilot,
            'g' => $f->glider,
        };
    }

    public function getSubScorable(flight $f): club|manufacturer
    {
        return match ($this->class_table_alias) {
            'p' => $f->club,
            'g' => $f->glider->manufacturer,
        };
    }

    public function getID(flight $f): int
    {
        return match ($this->class_table_alias) {
            'p' => $f->pilot->pid,
            'g' => $f->glider->gid,
        };
    }

    public function getTitle(flight $f): string
    {
        return match ($this->class_table_alias) {
            'p' => $f->pilot->name,
            'g' => $f->glider->name,
        };
    }

    public function getSubTitle(flight $f): string
    {
        return match ($this->class_table_alias) {
            'p' => $f->club->title,
            'g' => $f->glider->manufacturer->title,
        };
    }

    public function getTertiaryTitle(flight $f): string
    {
        return match ($this->class_table_alias) {
            'p' => $f->glider->name,
            'g' => '',
        };
    }
}
