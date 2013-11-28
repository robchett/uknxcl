<?php
namespace module\tables\object;

use classes\get;
use classes\table_array;
use html\node;
use object\flight;
use object\flight_type;
use object\launch_type;
use object\pilot;
use object\pilot_official;

class league_table {

    /** @var \classes\table_array */
    public $flights = null;
    public $league = null;
    public $where = [];
    public $show_top_4 = false;
    public $handicap = false;
    public $show_multipliers = false;
    public $ScoreType = 'score';
    public $OrderBy = 'score';
    public $Title = null;
    public $class = '\object\pilot';
    public $class_primary_key = 'pid';
    public $year_title = 'All Time';
    public $class_table_alias = 'p';
    public $official_class = '\object\pilot_official';
    public $SClass = '\object\club';
    public $S_alias = 'c';
    public $max_flights = 6;
    public $KP_Mod = 1;
    public $C5_Mod = 1;
    public $WHERE = '';
    public $date = null;
    public $in = [];
    public $parameters = [];
    /** @var league_table_options */
    public $options;
    private $modifier_string = '';
    /** @var result */
    private $result;

    function __construct(\stdClass $settings = null) {
        $this->set_default($settings);
    }

    public function set_default($settings) {
        $this->options = new league_table_options($settings, $this);
    }

    public static function decode_url($url) {
        $object = new \stdClass();
        $decode = urldecode($url);
        $decode_parts = explode(',', $decode);
        foreach ($decode_parts as $part) {
            $key_val = explode('-', $part, 2);
            if (count($key_val) == 2) {
                $object->{$key_val[0]} = $key_val[1];
            }
        }
        return $object;
    }

    public static function encode_url($parts) {
        $json = str_replace([':', '"'], ['-', ''], trim(json_encode($parts), '{}'));
        return urlencode($json);
    }

    public function get_url() {
        $ignore_fields = [
            'parent',
            'layout'
        ];
        $url_parts = new \stdClass();
        $default = new league_table_options(null, $this);
        foreach ($this->options as $option => $value) {
            if (!in_array($option, $ignore_fields) && ((!isset($default->$option) && !is_null($value)) || $default->$option != $value)) {
                $url_parts->$option = $value;
            }
        }
        $url = '/tables/' . $this->get_layout_url() . self::encode_url($url_parts);
        return $url;
    }

    public function get_primary_key() {
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
        if (isset ($this->in ['t'])) {
            $this->options->layout = $this->in ['t'];
        }
        if (isset ($this->in ['pages'])) {
            $this->options->set_official(1);
            $this->Title .= ' Official';
        }
        if (isset ($this->in ['base'])) {
            $this->ScoreType = 'base_score';
        }
        if (isset ($this->in ['pilot'])) {
            $this->options->set_pilot_id($this->in ['pilot']);
        }
        if (isset ($this->in ['kp']) && is_numeric($this->in ['kp'])) {
            $this->KP_Mod = $this->in ['kp'];
        }
        if (isset ($this->in ['c5']) && is_numeric($this->in ['c5'])) {
            $this->C5_Mod = $this->in ['c5'];
        }
        if (isset ($this->in ['Min']) && is_numeric($this->in ['Min'])) {
            $this->options->minimum_score = $this->in ['Min'];
        }
        if (isset ($this->in ['View'])) {
            $this->options->set_official(1);
        }
        if (isset ($this->in ['league']) && $this->in ['league'] == 'hangies') {
            $this->where[] = 'hangies = 1';
            $this->options->minimum_score = 0;
        }
        if (isset ($this->in ['cls'])) {
            $this->options->set_class($this->in ['cls']);
            $this->Title .= ' Class ' . $this->in['cls'];
        }
        if (isset ($this->in ['win'])) {
            $this->options->set_winter($this->in ['win']);
        }
        if (isset ($this->in ['def'])) {
            $this->options->set_defined($this->in ['def']);
        }
        if (isset ($this->in ['gen'])) {
            $this->options->set_gender($this->in ['gen']);
        }
        if (isset ($this->in ['c3d'])) {
            $this->options->set_dimensions($this->in['c3d']);
        }
        if (isset ($this->in ['rdg'])) {
            $this->options->set_rigid($this->in['rgd']);
        }
        if (isset ($this->in ['date'])) {
            $this->where[] .= 'date = :date';
            $this->parameters['date'] = $this->in['date'];
            // $this->options->layout = league_table_options::LAYOUT_TOP_TEN;
        }
        if (isset ($this->in ['noMulti'])) {
            $this->options->use_multipliers = false;
        }
// Removing launch and flight type from sql.
        if (isset($this->in['launch'])) {
            if (!in_array('w', $this->in['launch'])) {
                $this->options->remove_launch(launch_type::WINCH);
            }
            if (!in_array('f', $this->in['launch'])) {
                $this->options->remove_launch(launch_type::FOOT);
            }
            if (!in_array('a', $this->in['launch'])) {
                $this->options->remove_launch(launch_type::AERO);
            }
        }
        if (isset($this->in['flight_type'])) {
            if (!in_array('od', $this->in['flight_type'])) {
                $this->options->remove_flight_type(flight_type::OD_ID);
            }
            if (!in_array('or', $this->in['flight_type'])) {
                $this->options->remove_flight_type(flight_type::OR_ID);
            }
            if (!in_array('go', $this->in['flight_type'])) {
                $this->options->remove_flight_type(flight_type::GO_ID);
            }
            if (!in_array('tr', $this->in['flight_type'])) {
                $this->options->remove_flight_type(flight_type::TR_ID);
            }
            if (!in_array('ft', $this->in['flight_type'])) {
                $this->options->remove_flight_type(flight_type::FT_ID);
            }
        }

// Choose Classes (glider/pilot), and append official if needed  - default pilot
        if (isset ($this->in ['object']) && $this->in ['object'] == 'Glider') {
            $this->options->glider_mode = true;
        }

        $this->show_top_4 = isset ($this->in ['show_top_4']) ? true : false;
        $this->options->split_classes = isset ($this->in ['split']) ? true : false;
        $this->handicap = isset ($this->in ['HK']) ? true : false;
    }

    public function set_year($year_string) {
        $this->options->set_year($year_string);
    }

    public function set_glider_view() {
        $this->class = '\object\glider';
        $this->class_primary_key = 'gid';
        $this->class_table_alias = 'g';
        $this->official_class = '\object\glider_official';
        $this->SClass = 'manufacturer';
        $this->S_alias = 'gm';
    }

    public function use_preset($type) {
        switch ($type) {
            case(0):
                $this->options->set_official(1);
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
                $this->options->use_multipliers = false;
                break;

            case(16):
                $this->options->layout = league_table_options::LAYOUT_RECORDS;
                break;
        }
    }

    public function get_layout_url() {
        switch ($this->options->layout) {
            case league_table_options::LAYOUT_LEAGUE:
                return '';
            case league_table_options::LAYOUT_CLUB:
                return 'club/';
            case league_table_options::LAYOUT_PILOT_LOG:
                return 'pilot_log/';
            case league_table_options::LAYOUT_TOP_TEN:
                return 'top_ten/';
            case league_table_options::LAYOUT_RECORDS:
                return 'records/';
        }
        return '';
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
        }
    }

    public function generate_csv() {
        $flights = flight::get_all(['p.pid AS p_pid', 'p.name AS p_name', 'c.title AS c_title', 'g.class AS g_class', 'g.name AS g_name', 'score', 'defined', 'lid'], ['join' => ['glider g' => 'flight.gid=g.gid', 'pilot p' => 'p.pid = flight.pid', 'club c' => 'c.cid=flight.cid'], 'where' => '`delayed`=0 AND personal=0 AND score>10 AND season = 2012']);
        $array = new table_array();
        $flights->iterate(
            function (flight $flight) use (&$array) {
                if (isset ($array [$flight->p_pid]))
                    $array [$flight->p_pid]->add_flight($flight);
                else {
                    $array [$flight->p_pid] = new pilot_official();
                    $array [$flight->p_pid]->set_from_flight($flight, 6, 0);
                    $array [$flight->p_pid]->output_function = 'csv';
                }
            }
        );
        $array->uasort(['\module\tables\object\league_table', 'cmp']);
        $class1 = $class5 = 1;
        echo node::create('pre', [], '
        Pos ,Name ,Glider ,Club ,Best ,Second ,Third ,Forth ,Fifth ,Sixth ,Total' . "\n" .
            $array->iterate_return(
                function (pilot $pilot) use (&$class1, &$class5) {
                    if ($pilot->class == 1) {
                        $class1++;
                        return $pilot->output($class1, 0);
                    } else {
                        $class5++;
                        return $pilot->output($class5, 0);
                    }
                }
            )
        );
        die();
    }

    public function get_table() {
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
                $this->options->minimum_score = 0;
                break;
            case(league_table_options::LAYOUT_TOP_TEN):
                $this->result = new result_top_ten();
                $this->Title .= ' Top 10s';
                break;
            case(league_table_options::LAYOUT_RECORDS):
                $this->result = new result_records();
                return $this->result->make_table($this);
            case(5):
                $this->OrderBy = 'date';
                $this->result = new result_list();
                $this->Title .= ' List';
                break;
        }

        if ($this->options->official) {
            $this->class = $this->official_class;
        }

        $this->options->get_sql();

        $this->get_flights();
        return $this->result->make_table($this);

    }

    public function set_modifier_string() {
        $this->modifier_string = $this->ScoreType . ($this->handicap ? ' * IF(g.kingpost,' . $this->KP_Mod . ',1) * IF(g.class = 5,' . $this->C5_Mod . ',1) ' : '');
    }

    public function get_flights() {
        $this->set_modifier_string();

        $this->where[] = '`delayed`=0';
        $this->where = implode(' AND ', $this->where);
        if (isset ($this->type) && $this->type == 2) {
            $this->where .= ' AND p.pid=' . $this->options->pilot_id;
            $this->OrderBy = "date";
        } else {
            $this->where .= " AND personal=0 ";
        }

        $this->flights = flight::get_all(['fid', 'p.pid AS p_pid', 'g.gid', $this->class_table_alias . '.' . $this->class_primary_key . ' AS ClassID', 'p.name AS p_name', $this->S_alias . '.title AS c_name', 'g.class AS class', 'g.name AS g_name', 'gm.title AS gm_title', 'g.kingpost AS g_kingpost', 'did', 'defined', 'lid', 'multi', 'ftid', $this->modifier_string . ' AS score', 'date', 'coords'],
            [
                'join' => [
                    'glider g' => 'flight.gid=g.gid',
                    'club c' => 'flight.cid=c.cid',
                    'pilot p' => 'flight.pid=p.pid',
                    'manufacturer gm' => 'g.mid = gm.mid'
                ],
                'where' => (is_array($this->where) ? implode(' AND ', $this->where) : $this->where),
                'order' => $this->OrderBy . ' DESC',
                'parameters' => $this->parameters,
            ]
        );
    }

    function write_table_header($flights, $type = 'pid') {
        $inner_html = '';
        foreach (range(1, $flights) as $pos) {
            $inner_html .= node::create('th', [], get::ordinal($pos));

        }
        $html =
            node::create('thead', [],
                node::create('tr', [],
                    node::create('th.pos', [], 'Pos') .
                    node::create('th.name', [], 'Name') .
                    node::create('th.club', [], ($type == 'pid' ? 'Club / Glider' : 'Manufacturer')) .
                    $inner_html .
                    node::create('th.tot', [], 'Total')
                )
            );
        return $html;
    }

    function getFlights($year) {
        if ($year >= 2003 || $year == "All Time") {
            return 6;
        } else
            return 5;
    }

    /**
     * Generates a sub table showing the top flights in each category.
     * @param string $where - a complete sql WHERE clause
     * @param array $params
     * @return string
     */
    function ShowTop4($where, $params = []) {
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
                        'join' => flight::$default_joins,
                        'where' => $where_extend,
                        'order' => 'score DESC',
                        'parameters' => $params
                    ]
                );
                if (isset($flight->fid) && $flight->fid) {
                    $prefix = $flight->name . ' (' . ($flight->class == 5 ? 'R' : 'F') . ') ';
                    $html .= $flight->to_print($prefix)->get();
                } else
                    $html .= node::create('td', [], 'No Flights Fit');
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


    public static function cmp($a, $b) {
        if ($a->score == $b->score) {
            return 0;
        }
        return ($a->score > $b->score) ? -1 : 1;
    }
}