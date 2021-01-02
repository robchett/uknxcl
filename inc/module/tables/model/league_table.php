<?php

namespace module\tables\model;

use classes\get;
use classes\interfaces\model_interface;
use classes\table_array;
use html\node;
use JetBrains\PhpStorm\NoReturn;
use model\flight;
use model\flight_type;
use model\launch_type;
use model\pilot;
use model\pilot_official;
use model\scorable;
use stdClass;

class league_table implements model_interface {

    /** @var flight[] */
    public ?table_array $flights = null;
    public $league = null;
    public array $where = [];
    public bool $show_multipliers = false;
    public string $ScoreType = 'score';
    public string $OrderBy = 'score';
    public $Title = null;
    public string $class = '\model\pilot';
    public string $class_primary_key = 'pid';
    public string $year_title = 'All Time';
    public string $class_table_alias = 'p';
    public string $official_class = '\model\pilot_official';
    public string $SClass = '\model\club';
    public string $S_alias = 'c';
    public int $max_flights = 6;
    public string $WHERE = '';
    public $date = null;
    public array $in = [];
    public array $parameters = [];
    public string $base_url = '/tables';
    /** @var league_table_options */
    public league_table_options $options;
    private string $modifier_string = '';
    /** @var result */
    private result $result;

    function __construct(stdClass $settings = null) {
        $this->set_default($settings);
    }

    public function set_default($settings) {
        $this->options = new league_table_options($settings, $this);
    }

    public static function decode_url($url): stdClass {
        $object = new stdClass();
        $decode = urldecode($url);
        $decode_parts = explode(',', $decode);
        foreach ($decode_parts as $part) {
            $key_val = explode('~', $part, 2);
            if (count($key_val) == 2) {
                if (strpos($key_val[1], '[') === 0) {
                    $key_val[1] = explode('|', substr($key_val[1], 1, strlen($key_val[1]) - 2));
                }
                $object->{$key_val[0]} = $key_val[1];
            }
        }
        return $object;
    }

    public static function cmp($a, $b): int {
        if ($a->score == $b->score) {
            return 0;
        }
        return ($a->score > $b->score) ? -1 : 1;
    }

    public function get_show_all_url(): array|string {
        return str_replace('/tables/', '/mass_overlay/', $this->get_url());
    }

    public function get_url(): string {
        $ignore_fields = [
            'parent',
            'layout',
        ];
        $url_parts = new stdClass();
        $default = new league_table_options(null, $this);
        foreach ($this->options as $option => $value) {
            if (!in_array($option, $ignore_fields) && ((!isset($default->$option) && !is_null($value)) || $default->$option != $value)) {
                $url_parts->$option = $value;
            }
        }
        return $this->base_url . '/' . $this->get_layout_url() . self::encode_url($url_parts);
    }

    public function get_layout_url(): string {
        switch ($this->options->layout) {
            case league_table_options::LAYOUT_LEAGUE:
                return '';
            case league_table_options::LAYOUT_CLUB:
                return 'club/';
            case league_table_options::LAYOUT_PILOT_LOG:
                return 'pilot_log/';
            case league_table_options::LAYOUT_TOP_TEN:
                return 'top_ten/';
            case league_table_options::LAYOUT_LIST:
                return 'list/';
            case league_table_options::LAYOUT_RECORDS:
                return 'records/';
        }
        return '';
    }

    public static function encode_url($parts): string {
        foreach ($parts as &$part) {
            if (is_array($part)) {
                $part = str_replace([',', '"'], ['|', ''], json_encode($part));
            }
        }
        $json = str_replace([':', '"'], ['~', ''], trim(json_encode($parts), '{}'));
        return urlencode($json);
    }

    public function get_primary_key(): bool|string {
        return hash('md5', $this->get_url());
    }

    public function set_from_request() {
        $this->in = $_REQUEST;
        if (isset ($this->in ['year']) && $this->in ['year'] != '') {
            $this->set_year($this->in ['year']);
        } else {
            $this->Title = 'All Time';
        }
        if (isset ($this->in ['Flights'])) {
            $this->max_flights = $this->in ['Flights'];
        }
        if (isset ($this->in ['layout'])) {
            $this->options->layout = $this->in ['layout'];
        }
        if (isset ($this->in ['pages'])) {
            $this->options->set_official(1);
            $this->Title .= ' Official';
        }
        if (isset ($this->in ['base'])) {
            $this->ScoreType = 'base_score';
        }
        if (isset ($this->in ['pilot']) && $this->in['layout'] == 2) {
            $this->options->set_pilot_id($this->in ['pilot']);
        }
        if (isset ($this->in ['handicap_kingpost']) && is_numeric($this->in ['handicap_kingpost'])) {
            $this->options->handicap_kingpost = $this->in ['handicap_kingpost'];
        }
        if (isset ($this->in ['handicap_rigid']) && is_numeric($this->in ['handicap_rigid'])) {
            $this->options->handicap_rigid = $this->in ['handicap_rigid'];
        }
        if (isset ($this->in ['minimum_score'])) {
            $this->options->set_minimum_score($this->in ['minimum_score']);
        }
        if (isset ($this->in ['official'])) {
            $this->options->set_official(1);
        }
        if (isset ($this->in ['league']) && $this->in ['league'] == 'hangies') {
            $this->where[] = 'hangies = 1';
            $this->options->set_minimum_score(0);
        }
        if (isset ($this->in ['glider_class'])) {
            $this->options->set_class($this->in ['glider_class']);
            $this->Title .= ' Class ' . $this->in['glider_class'];
        }
        if (isset ($this->in ['winter'])) {
            $this->options->set_winter($this->in ['winter']);
        }
        if (isset ($this->in ['defined'])) {
            $this->options->set_defined($this->in ['defined']);
        }
        if (isset ($this->in ['gender'])) {
            $this->options->set_gender($this->in ['gender']);
        }
        if (isset ($this->in ['dimensions'])) {
            $this->options->set_dimensions($this->in['dimensions']);
        }
        if (isset ($this->in ['ridge'])) {
            $this->options->set_ridge($this->in['ridge']);
        }
        if (isset ($this->in ['Date'])) {
            $this->options->set_date($this->in['Date']);
        }
        if (isset ($this->in ['no_multipliers'])) {
            $this->options->no_multipliers = true;
        }
        // Removing launch and flight type from sql.
        if (isset($this->in['launches'])) {
            if (!in_array(launch_type::WINCH, $this->in['launches'])) {
                $this->options->remove_launch(launch_type::WINCH);
            }
            if (!in_array(launch_type::FOOT, $this->in['launches'])) {
                $this->options->remove_launch(launch_type::FOOT);
            }
            if (!in_array(launch_type::AERO, $this->in['launches'])) {
                $this->options->remove_launch(launch_type::AERO);
            }
        }
        if (isset($this->in['types'])) {
            if (!in_array(flight_type::OD_ID, $this->in['types'])) {
                $this->options->remove_flight_type(flight_type::OD_ID);
            }
            if (!in_array(flight_type::OR_ID, $this->in['types'])) {
                $this->options->remove_flight_type(flight_type::OR_ID);
            }
            if (!in_array(flight_type::GO_ID, $this->in['types'])) {
                $this->options->remove_flight_type(flight_type::GO_ID);
            }
            if (!in_array(flight_type::TR_ID, $this->in['types'])) {
                $this->options->remove_flight_type(flight_type::TR_ID);
            }
            if (!in_array(flight_type::FT_ID, $this->in['types'])) {
                $this->options->remove_flight_type(flight_type::FT_ID);
            }
        }

        // Choose Classes (glider/pilot), and append official if needed  - default pilot
        if (isset ($this->in ['object']) && $this->in ['object'] == 'Glider') {
            $this->options->glider_mode = true;
        }

        if (isset($this->in ['flown_through'])) {
            $this->options->set_flown_through($this->in ['flown_through']);
        }

        $this->options->show_top_4 = isset ($this->in ['show_top_4']);
        $this->options->split_classes = isset ($this->in ['split_classes']);
        $this->options->handicap = isset ($this->in ['handicap']);
    }

    public function set_year($year_string) {
        $this->options->set_year($year_string);
    }

    public function set_glider_view() {
        $this->class = '\model\glider';
        $this->class_primary_key = 'gid';
        $this->class_table_alias = 'g';
        $this->official_class = '\model\glider_official';
        $this->SClass = '\model\manufacturer';
        $this->S_alias = 'gm';
    }

    public function use_preset($type, $year) {
        switch ($type) {
            case(0):
                $this->options->set_official(1);
                if ($year > 2012) {
                    $this->options->set_dimensions(1);
                }
                break;

            case(1):
                $this->options->remove_launch(launch_type::AERO);
                $this->options->remove_launch(launch_type::WINCH);
                break;

            case(2):
                $this->options->remove_launch(launch_type::FOOT);
                $this->options->remove_launch(launch_type::WINCH);
                break;

            case(3):
                $this->options->remove_launch(launch_type::AERO);
                $this->options->remove_launch(launch_type::FOOT);
                break;

            case(4):
                $this->options->set_winter(1);
                break;

            case(5):
                $this->options->set_defined(1);
                break;

            case(6):
                $this->options->set_gender('F');
                break;

            case(7):
                $this->options->layout = league_table_options::LAYOUT_CLUB;
                break;
            case(8):
                $this->options->set_official(0);
                $this->options->layout = league_table_options::LAYOUT_CLUB;
                break;

            case(9):
                $this->options->layout = league_table_options::LAYOUT_TOP_TEN;
                break;

            case(10):
                $this->options->layout = league_table_options::LAYOUT_PILOT_LOG;
                break;

            case(11):
                $this->options->set_dimensions(3);
                $this->options->set_official(1);
                break;

            case(12):
                $this->where[] = 'hangies = 1';
                break;

            case(13):
                $this->options->set_class(5);
                $this->options->set_official(1);
                break;

            case(14):
                $this->options->set_class(1);
                $this->options->set_official(1);
                break;

            case(15):
                $this->options->layout = league_table_options::LAYOUT_TOP_TEN;
                $this->options->no_multipliers = true;
                break;

            case(16):
                $this->options->layout = league_table_options::LAYOUT_RECORDS;
                break;

            case(17):
                $this->options->set_club_id(31);
                $this->options->set_launched_from('SD,SE,NY');
                break;
        }
    }

    public function set_layout_from_url($url) {
        switch ($url) {
            case '':
                $this->options->layout = league_table_options::LAYOUT_LEAGUE;
                break;
            case 'club':
                $this->options->layout = league_table_options::LAYOUT_CLUB;
                break;
            case 'pilot_log':
                $this->options->layout = league_table_options::LAYOUT_PILOT_LOG;
                break;
            case 'top_ten':
                $this->options->layout = league_table_options::LAYOUT_TOP_TEN;
                break;
            case'records':
                $this->options->layout = league_table_options::LAYOUT_RECORDS;
                break;
            case'list':
                $this->options->layout = league_table_options::LAYOUT_LIST;
                break;
        }
    }

    #[NoReturn]
    public function generate_csv() {
        $flights = flight::get_all([
            'p.pid AS p_pid',
            'p.name AS p_name',
            'c.title AS c_title',
            'g.class AS g_class',
            'g.name AS g_name',
            'score',
            'defined',
            'lid'], [
            'join'  => [
                'glider g' => 'flight.gid=g.gid',
                'pilot p'  => 'p.pid = flight.pid',
                'club c'   => 'c.cid=flight.cid'],
            'where' => '`delayed`=0 AND personal=0 AND score>10 AND season = 2012']);
        $array = new table_array();
        $flights->iterate(
            function (flight $flight) use (&$array) {
                if (isset ($array [$flight->p_pid])) {
                    $array [$flight->p_pid]->add_flight($flight);
                } else {
                    $array [$flight->p_pid] = new pilot_official();
                    $array [$flight->p_pid]->set_from_flight($flight, 6, 0);
                    $array [$flight->p_pid]->output_function = 'csv';
                }
            }
        );
        $array->uasort(['\module\tables\model\league_table', 'cmp']);
        $class1 = $class5 = 1;
        echo node::create('pre', [], "Pos ,Name ,Glider ,Club ,Best ,Second ,Third ,Forth ,Fifth ,Sixth ,Total\n" .
            $array->iterate_return(function (scorable $pilot) use (&$class1, &$class5) {
                if ($pilot->class == 1) {
                    $class1++;
                    return $pilot->output($class1);
                } else {
                    $class5++;
                    return $pilot->output($class5);
                }
            })
        );
        die();
    }

    public function get_table(): string {
        if ($this->options->pilot_id) {
            $this->options->layout = league_table_options::LAYOUT_PILOT_LOG;
        }
        switch ($this->options->layout) {
            case(league_table_options::LAYOUT_LEAGUE):
                $this->result = new result_league();
                $this->Title .= ' League';
                break;
            case(league_table_options::LAYOUT_CLUB):
                $this->result = new result_club();
                $this->Title .= ' Club League';
                break;
            case(league_table_options::LAYOUT_PILOT_LOG):
                $this->result = new result_pilot();
                $pilot = new pilot();
                $pilot->do_retrieve_from_id(['name'], $this->options->pilot_id);
                $this->Title .= ' Pilot Log (' . $pilot->name . ')';
                $this->options->set_minimum_score(0);
                break;
            case(league_table_options::LAYOUT_TOP_TEN):
                $this->result = new result_top_ten();
                $this->Title .= ' Top 10s';
                break;
            case(league_table_options::LAYOUT_RECORDS):
                $this->result = new result_records();
                return $this->result->make_table($this);
            case(league_table_options::LAYOUT_LIST):
                $this->OrderBy = 'date';
                $this->result = new result_list();
                $this->Title .= ' List';
                break;
        }

        if ($this->options->official) {
            $this->class = $this->official_class;
        }

        $this->get_flights();
        return $this->result->make_table($this);

    }

    public function set_modifier_string() {
        $this->modifier_string = $this->ScoreType . ($this->options->handicap ? ' * IF(g.kingpost,' . $this->options->handicap_kingpost . ',1) * IF(g.class = 5,' . $this->options->handicap_rigid . ',1) ' : '');
    }

    function write_table_header($flights, $type = 'pid'): string {
        $inner_html = '';
        foreach (range(1, $flights) as $pos) {
            $inner_html .= node::create('th.left', [], get::ordinal($pos));

        }
        return node::create('thead', [],
            node::create('tr', [],
                "<th class='pos left'>Pos</th><th class='name left'>Name</th>" .
                node::create('th.club.left', [], ($type == 'pid' ? 'Club / Glider' : 'Manufacturer')) .
                $inner_html .
                "<th class='tot left'>Total</th>"
            )
        );
    }

    function getFlights($year): int {
        if ($year >= 2003 || $year == "All Time") {
            return 6;
        } else {
            return 5;
        }
    }

    /**
     * @return table_array|flight[]
     */
    public function get_flights(): array|table_array {
        if (!$this->flights) {
            $this->options->get_sql();
            $this->set_modifier_string();

            $this->where[] = '`delayed`=0';
            $where = implode(' AND ', $this->where);
            if (isset ($this->type) && $this->type == league_table_options::LAYOUT_PILOT_LOG) {
                $where .= ' AND p.pid=' . $this->options->pilot_id;
                $this->OrderBy = "date";
            } else {
                $where .= " AND personal=0 ";
            }

            $this->flights = flight::get_all([
                'fid',
                'p.pid AS p_pid',
                'g.gid',
                $this->class_table_alias . '.' . $this->class_primary_key . ' AS ClassID',
                'p.name AS p_name',
                $this->S_alias . '.title AS c_name',
                'g.class AS class',
                'g.name AS g_name',
                'gm.title AS gm_title',
                'g.kingpost AS g_kingpost',
                'did',
                'defined',
                'lid',
                'multi',
                'ftid',
                $this->modifier_string . ' AS score',
                'date',
                'coords'],
                [
                    'join'       => [
                        'glider g'        => 'flight.gid=g.gid',
                        'club c'          => 'flight.cid=c.cid',
                        'pilot p'         => 'flight.pid=p.pid',
                        'manufacturer gm' => 'g.mid = gm.mid',
                    ],
                    'where'      => $where,
                    'order'      => $this->OrderBy . ' DESC',
                    'parameters' => $this->parameters,
                ]
            );
        }
        return $this->flights;
    }

    /**
     * Generates a sub table showing the top flights in each category.
     *
     * @param $where - a complete sql WHERE clause
     * @param array $params
     *
     * @return string
     */
    public function ShowTop4($where, array $params = []): string {
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
                $where_extend .= ' AND ftid=:ftid';
                $params['ftid'] = $i;
                $flight = new flight();
                $flight->do_retrieve(['flight.*', $this->class . '.name AS name', 'glider.class AS class'], [
                        'join'       => flight::$default_joins,
                        'where'      => $where_extend,
                        'order'      => 'score DESC',
                        'parameters' => $params,
                    ]
                );
                if (isset($flight->fid) && $flight->fid) {
                    $prefix = $flight->name . ' (' . ($flight->class == 5 ? 'R' : 'F') . ') ';
                    $html .= (string)$flight->to_print($prefix);
                } else {
                    $html .= "<td>No Flights Fit<td>";
                }
            }
            $html .= '</tr>';
        }

        return node::create('table.top4.main.results', [],
            node::create('thead', [],
                node::create('tr', [],
                    node::create('th', ['style' => 'width:52px'], 'Category') .
                    node::create('th', ['style' => 'width:155px;color:black'], 'Open Dist') .
                    node::create('th', ['style' => 'width:155px;color:green'], 'Out & Return') .
                    node::create('th', ['style' => 'width:155px;color:red'], 'Goal') .
                    node::create('th', ['style' => 'width:155px;color:blue'], 'Triangle')
                )
            ) .
            $html
        );
    }
}