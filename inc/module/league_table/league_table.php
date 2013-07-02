<?

class league_table {
    /** @var flight_array */
    public $flights = null;
    public $official = 0;
    public $pid;
    public $type = 0;
    public $TFlight = 1;
    public $launches = array(1, 2, 3);
    public $types = array(1, 2, 3, 4);
    public $winter = null;
    public $defined = null;
    public $gender = null;
    public $dimensions = null;
    public $league = null;
    public $use_multipliers = true;
    public $where = array();
    public $year = null; // if not set in GET use all-time
    public $show_top_4 = false;
    public $split_classes = false;
    public $handicap = false;
    public $show_multipliers = false;
    public $ScoreType = 'score';
    public $OrderBy = 'score';
    public $Title = null;
    public $class = 'pilot';
    public $class_primary_key = 'pid';
    public $class_table_alias = 'p';
    public $official_class = 'pilot_official';
    public $SClass = 'club';
    public $max_flights = 6;
    public $KP_Mod = 1;
    public $C5_Mod = 1;
    public $min_score = 10;
    public $WHERE = '';
    public $date = null;
    public $in = array();
    public $parameters = array();
    private $modifier_string = '';

    function __construct() {
        $this->set_default();
    }

    public function set_default() {

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
            $this->type = $this->in ['t'];
        }
        if (isset ($this->in ['pages'])) {
            $this->official = true;
            $this->Title .= ' Official';
        }
        if (isset ($this->in ['base'])) {
            $this->ScoreType = 'base_score';
        }
        if (isset ($this->in ['pilot'])) {
            $this->pid = $this->in ['pilot'];
        }
        if (isset ($this->in ['kp']) && is_numeric($this->in ['kp'])) {
            $this->KP_Mod = $this->in ['kp'];
        }
        if (isset ($this->in ['c5']) && is_numeric($this->in ['c5'])) {
            $this->C5_Mod = $this->in ['c5'];
        }
        if (isset ($this->in ['Min']) && is_numeric($this->in ['Min'])) {
            $this->min_score = $this->in ['Min'];
        }
        if (isset ($this->in ['View'])) {
            $this->official = true;
        }
        if (isset ($this->in ['league']) && $this->in ['league'] == 'hangies') {
            $this->where[] = 'hangies = 1';
            $this->min_score = 0;
        }
        if (isset ($this->in ['cls'])) {
            if ($this->in ['cls'] == 5 || $this->in ['cls'] == 1) {
                $this->where[] = 'class = :class';
                $this->parameters['class'] = $this->in['cls'];
                $this->Title .= ' Class ' . $this->in['cls'];
            }
        }
        if (isset ($this->in ['win'])) {
            if (is_numeric($this->in ['win']) && $this->in['win']) {
                $this->where[] = 'winter = :winter';
                $this->parameters['winter'] = $this->in['win'];
            }
        }
        if (isset ($this->in ['def'])) {
            if (is_numeric($this->in ['def']) && $this->in['def']) {
                $this->where[] = 'defined = :defined';
                $this->parameters['defined'] = $this->in['def'];
            }
        }
        if (isset ($this->in ['gen'])) {
            if ($this->in ['gen'] == 'F' || $this->in ['gen'] == 'M') {
                $this->where[] = 'gender = :gender';
                $this->parameters['gender'] = $this->in['gen'];
            }
        }
        if (isset ($this->in ['c3d'])) {
            if (is_numeric($this->in ['c3d'])) {
                if ($this->in ['c3d'] == 1) {
                    $this->where[] = 'did > 1';
                } else if ($this->in ['c3d'] != 0) {
                    $this->where[] = 'did = :did';
                    $this->parameters['did'] = $this->in['c3d'];
                }
            }
        }
        if (isset ($this->in ['rdg'])) {
            if (is_numeric($this->in ['rdg']) && $this->in['rgd']) {
                $this->where[] .= 'ridge = :ridge';
                $this->parameters['ridge'] = $this->in['rdg'];
            }
        }
        if (isset ($this->in ['date'])) {
            $this->where[] .= 'date = :date';
            $this->parameters['date'] = $this->in['date'];
            $this->type = 3;
        }
        if (isset ($this->in ['noMulti'])) {
            $this->ScoreType = "base_score";
            $this->OrderBy = "base_score";
        }
// Removing launch and flight type from sql.
        if (isset($this->in['launch'])) {
            if (!in_array('w', $this->in['launch'])) {
                $this->where[] = 'lid != 3';
            }
            if (!in_array('f', $this->in['launch'])) {
                $this->where[] = 'lid != 1';
            }
            if (!in_array('a', $this->in['launch'])) {
                $this->where[] = 'lid != 2';
            }
        }
        if (isset($this->in['flight_type'])) {
            if (!in_array('od', $this->in['flight_type'])) {
                $this->where[] = 'ftid != 1';
            }
            if (!in_array('or', $this->in['flight_type'])) {
                $this->where[] = 'ftid != 2';
            }
            if (!in_array('go', $this->in['flight_type'])) {
                $this->where[] = 'ftid != 3';
            }
            if (!in_array('tr', $this->in['flight_type'])) {
                $this->where[] = 'ftid != 4';
            }
            if (!in_array('ft', $this->in['flight_type'])) {
                $this->where[] = 'ftid != 5';
            }
        }

// Choose Classes (glider/pilot), and append official if needed  - default pilot
        if (isset ($this->in ['object']) && $this->in ['object'] == 'Glider') {
            $this->set_glider_view();
        }

        $this->show_top_4 = isset ($this->in ['TFlight']) ? true : false;
        $this->split_classes = isset ($this->in ['split']) ? true : false;
        $this->handicap = isset ($this->in ['HK']) ? true : false;
    }

    public function  set_year($year_string) {
        $param_count = 0;
        $parts = array();

        $groups = explode(',', $year_string);
        foreach ($groups as $group) {
            $c = explode('-', $group);
            if (count($c) > 1 && count($c) < 3) {
                $parts[] = '(season>=:year' . $param_count . ' AND season<=:year' . ($param_count + 1) . ')';
                $this->parameters['year' . $param_count] = $c[0];
                $param_count++;
                $this->parameters['year' . $param_count] = $c[1];
                $param_count++;
            } else {
                $parts[] = 'season=:year' . $param_count;
                $this->parameters['year' . $param_count] = $group;
            }

            if ($parts) ;
            $this->where[] = '(' . implode('OR', $parts) . ')';
        }
    }

    public function set_glider_view() {
        $this->class = 'glider';
        $this->class_primary_key = 'gid';
        $this->class_table_alias = 'g';
        $this->official_class = 'glider_official';
        $this->SClass = 'manufacturer';
    }

    public function use_preset($type) {
        switch ($type) {
            case(0):
                $this->official = 1;
                break;

            case(1):
                $this->where[] = 'lid != 2';
                $this->where[] = 'lid != 3';
                break;

            case(2):
                $this->where[] = 'lid != 1';
                $this->where[] = 'lid != 3';
                break;

            case(3):
                $this->where[] = 'lid != 1';
                $this->where[] = 'lid != 2';
                break;

            case(4):
                $this->where[] = 'winter = 1';
                break;

            case(5):
                $this->where[] = 'defined = 1';
                break;

            case(6):
                $this->where[] = 'gender = 2';
                break;

            case(7):
                $this->official = 1;
                $this->type = 1;
                break;

            case(8):
                $this->type = 1;
                break;

            case(9):
                $this->type = 3;
                break;

            case(10):
                $this->type = 2;
                break;

            case(11):
                $this->dimensions = 3;
                $this->official = 1;
                break;

            case(12):
                $this->where[] = 'hangies = 1';
                break;

            case(13):
                $this->where[] = 'class = 5';
                $this->official = 1;
                $this->type = 0;
                break;

            case(14):
                $this->where[] = 'class = 1';
                $this->official = 1;
                $this->type = 0;
                break;

            case(15):
                $this->type = 3;
                $this->use_multipliers = false;
                break;

            case(16):
                $this->type = 5;
                break;
        }
    }

    public function generate_csv() {
        $flights = flight::get_all(array('p.pid', 'p.name', 'c.name', 'g.class', 'g.name', 'score', 'defined', 'lid'), array('join' => array('glider g' => 'flight.gid=g.gid', 'pilot p' => 'p.pid = flight.pid', 'club c' => 'c.cid=flight.cid'), 'where' => '`delayed`=0 AND personal=0 AND score>10 AND season = 2012'));
        /** @var  $array pilot_official[] */
        $array = array();
        /** @var  $flights flight[] */
        foreach ($flights as $t) {
            if (isset ($array [$t->p_pid]))
                $array [$t->p_pid]->add_flight($t);
            else {
                $array [$t->p_pid] = new pilot_official();
                $array [$t->p_pid]->set_from_flight($t, 6, 0);
                $array [$t->p_pid]->output_function = 'csv';
            }
        }
        usort($array, "cmp");
        $class1 = $class5 = 1;
        echo '<pre>Pos ,Name ,Glider ,Club ,Best ,Second ,Third ,Forth ,Fifth ,Sixth ,Total' . "\n";
        for ($j = 0; $j < sizeof($array); $j++) {
            if ($array [$j]->Class == 1) {
                echo $array [$j]->output($class1, 0);
                $class1++;
            } else {
                echo $array [$j]->output($class5, 0);
                $class5++;
            }
        }
        die();
    }

    public function get_table() {
        switch ($this->type) {
            case(0):
                include root . '/inc/module/league_table/view/custom_table.php';
                $this->Title .= ' League';
                break;
            case(1):
                include root . '/inc/module/league_table/view/custom_club.php';
                $this->Title .= ' Club League';
                break;
            case(2):
                include root . '/inc/module/league_table/view/custom_pilot.php';
                $pilot = new pilot();
                $pilot->do_retrieve_from_id(array('name'), $this->pid);
                $this->Title .= ' Pilot Log (' . $pilot->name . ')';
                $this->min_score = 0;
                break;
            case(3):
                include root . '/inc/module/league_table/view/TopTen.php';
                $this->Title .= ' Top 10s';
                break;
            case(4):
                $this->OrderBy = 'date';
                include root . '/inc/module/league_table/view/CustomList.php';
                $this->Title .= ' List';
                break;
            case(5):
                include root . '/inc/module/league_table/view/Records.php';
                return makeTable();
        }

        if ($this->official) {
            $this->class = $this->official_class;
        }

        $this->get_flights();
        return makeTable($this);

    }

    public function set_modifier_string () {
        $this->modifier_string = $this->ScoreType . ($this->handicap ? ' * IF(g.kingpost,' . $this->KP_Mod . ',1) * IF(g.class = 5,' . $this->C5_Mod . ',1) ' : '');
    }

    public function get_flights() {
        $this->set_modifier_string();

        $this->where[] = $this->ScoreType . ' > ' . $this->min_score;
        $this->where[] = '`delayed`=0';
        $this->where = implode(' AND ', $this->where);
        if (isset ($this->type) && $this->type == 2) {
            $this->where .= ' AND p.pid=' . $this->pid;
            $this->OrderBy = "date";
        } else {
            $this->where .= " AND personal=0 ";
        }

        $this->flights = flight::get_all(array('fid', 'p.pid', 'g.gid', $this->class_table_alias . '.' . $this->class_primary_key . ' AS ClassID', 'p.name', 'c.name', 'g.class', 'g.name', 'gm.title', 'g.kingpost', 'did', 'defined', 'lid', 'multi', 'ftid', $this->modifier_string . ' AS score', 'date', 'coords'),
            array(
                'join' => array(
                    'glider g' => 'flight.gid=g.gid',
                    'club c' => 'flight.cid=c.cid',
                    'pilot p' => 'flight.pid=p.pid',
                    'manufacturer gm' => 'g.mid = gm.mid'
                ),
                'where' => (is_array($this->where) ? implode(' AND ', $this->where) : $this->where),
                'order' => $this->OrderBy . ' DESC',
                'parameters' => $this->parameters,
            )
        );
    }

    function write_table_header($flights, $type = 'pid') {
        $inner_html = '';
        foreach (range(1, $flights) as $pos) {
            $inner_html .= '<th>' . get::ordinal($pos) . '</th>';

        }
        $html = '
        <table class="results main flights_' . $flights . '">
            <thead>
                <tr >
                <th class="pos">Pos</th>
                <th >Name</th>
                <th class=="club">' . ($type == 'pid' ? 'Club / Glider' : 'Manufacturer') . '</th>
                ' . $inner_html . '
                <th class="tot">Total</th>
            </tr>
        </thead>';
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
     */
    function ShowTop4($where, $params = array()) {
        $html = '';
        $launch_by_number = Array(1 => 'Foot', 2 => 'Aerotow', 3 => 'Winch');
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
                $flight->do_retrieve(array('flight.*', $this->class . '.name AS name', 'glider.class AS class'), array(
                        'join' => flight::$default_joins,
                        'where' => $where_extend,
                        'order' => 'score DESC',
                        'parameters' => $params
                    )
                );
                if (isset($flight->fid) && $flight->fid) {
                    $prefix = $flight->name . ' (' . ($flight->class == 5 ? 'R' : 'F') . ') ';
                    $html .= $flight->to_print($prefix)->get();
                } else
                    $html .= "<td>No Flights Fit.</td>";
            }
            $html .= '</tr>';
        }

        return '
<table class="top4 main results">
    <thead>
        <tr>
            <th style="width:52px">Catagory</th>
            <th style="width:155px;cplor:black" >Open Dist</th>
            <th style="width:155px;color:green">Out & Return</th>
            <th style="width:155px;color:red">Goal</th>
            <th style="width:155px;color:blue">Triangle</th>
        </tr>
    </thead>
        ' . $html . '
    <tbody>
    </tbody>
</table>';
    }

}

function cmp($a, $b) {
    if ($a->score == $b->score) {
        return 0;
    }
    return ($a->score > $b->score) ? -1 : 1;
}


?>
