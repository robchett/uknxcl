<?php
class league_table_options {

    /** @var league_table */
    public $parent;
    public $official = 1;
    public $pilot_id = null;
    public $launches = array(1, 2, 3);
    public $types = array(1, 2, 3, 4, 5);
    public $winter = null;
    public $defined = null;
    public $gender = null;
    public $dimensions = null;
    public $rigid = null;
    public $use_multipliers = true;
    public $year = null;
    public $glider_class = null;
    public $layout = 0;
    public $split_classes = false;
    public $glider_mode = false;
    public $minimum_score = 10;

    const LAYOUT_LEAGUE = 0;
    const LAYOUT_CLUB = 1;
    const LAYOUT_PILOT_LOG = 2;
    const LAYOUT_TOP_TEN = 3;
    const LAYOUT_RECORDS = 4;

    public function __construct($settings, $parent) {
        $this->parent = $parent;
        $this->year = date('Y');
        if (!is_null($settings)) {
            foreach ($settings as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function get_sql() {
        $this->get_winter();
        $this->get_defined();
        $this->get_gender();
        $this->get_dimensions();
        $this->get_rigid();
        $this->get_multipliers();
        $this->get_launch_string();
        $this->get_flight_type_string();
        $this->get_year();
        $this->get_pilot_id();
        $this->get_glider_mode();
    }

    public function add_launch($id) {
        if (!in_array($id, $this->launches)) {
            $this->launches[] = $id;
        }
    }

    public function remove_launch($id) {
        foreach ($this->launches as $key => $val) {
            if ($id == $val) {
                unset($this->launches[$key]);
                sort($this->launches);
                return true;
            }
        }
        return false;
    }

    public function get_launch_string() {
        if (count($this->launches) != 3) {
            $this->parent->where[] = '(lid = ' . implode('OR lid = ', $this->launches) . ')';
        }
    }

    public function remove_flight_type($id) {
        if (!in_array($id, $this->types)) {
            $this->types[] = $id;
        }
    }

    public function add_fight_type($id) {
        foreach ($this->types as $key => $val) {
            if ($id == $val) {
                unset($this->types[$key]);
                return true;
            }
        }
        return false;
    }

    public function get_flight_type_string() {
        if (count($this->types) != 5) {
            $this->parent->where[] = '(ftid = ' . implode('OR ftid = ', $this->types) . ')';
        }
    }

    public function set_winter($value) {
        if (is_numeric($value) && $value) {
            $this->winter = $value;
        }
    }

    public function get_winter() {
        if (!is_null($this->winter)) {
            $this->parent->where[] = 'winter=:winter';
            $this->parent->parameters['winter'] = $this->winter;
        }
    }

    public function set_defined($value) {
        if (is_numeric($value) && $value) {
            $this->defined = $value;
        }
    }

    public function get_defined() {
        if (!is_null($this->defined)) {
            $this->parent->where[] = '`defined` = :def';
            $this->parent->parameters['def'] = $this->defined;
        }
    }

    public function set_gender($value) {
        if ($value == 'F' || $value == 'M') {
            $this->gender = $value;
        }
    }

    public function get_gender() {
        if (!is_null($this->gender)) {
            $this->parent->where[] = 'gender = :gender';
            $this->parent->parameters['gender'] = $this->gender;
        }
    }

    public function set_dimensions($value) {
        if (is_numeric($value) && $value) {
            $this->dimensions = $value;
        }
    }

    public function get_dimensions() {
        if (!is_null($this->dimensions)) {
            if ($this->dimensions == 1) {
                $this->parent->where[] = 'did > 1';
            } else if ($this->dimensions != 0) {
                $this->parent->where[] = 'did = :did';
                $this->parent->parameters['did'] = $this->dimensions;
            }
        }
    }

    public function set_rigid($value) {
        if (is_numeric($value) && $value) {
            $this->rigid = $value;
        }
    }

    public function get_rigid() {
        if (!is_null($this->rigid)) {
            $this->parent->where[] = 'rigid=:rigid';
            $this->parent->parameters['rigid'] = $this->rigid;
        }
    }

    public function set_official($value) {
        if($value == 1 || $value == 0) {
            $this->official = $value;
        }
    }

    public function get_multipliers() {
        if (!$this->use_multipliers) {
            $this->parent->ScoreType = "base_score";
            $this->parent->OrderBy = "base_score";
        }
    }

    public function set_year($value) {
        $this->year = $value;
    }

    public function get_year() {
        if ($this->year != 'all_time') {
            $param_count = 0;
            $parts = array();
            $str_parts = array();

            $groups = explode(',', $this->year);
            foreach ($groups as $group) {
                $c = explode('-', $group);
                if (count($c) > 1 && count($c) < 3) {
                    $parts[] = '(season>=:year' . $param_count . ' AND season<=:year' . ($param_count + 1) . ')';
                    $str_parts[] = $c[0] . '-' . $c[1];
                    $this->parent->parameters['year' . $param_count] = $c[0];
                    $param_count++;
                    $this->parent->parameters['year' . $param_count] = $c[1];
                    $param_count++;
                } else {
                    $parts[] = 'season=:year' . $param_count;
                    $str_parts[] = $group;
                    $this->parent->parameters['year' . $param_count] = $group;
                }

            }
            if ($parts) {
                $this->parent->where[] = '(' . implode('OR', $parts) . ')';
                $this->parent->year_title = implode(',', $str_parts);
            }
        }
    }

    public function set_class($value) {
        if ($value == 1 || $value == 5) {
            $this->glider_class = $value;
        }
    }

    public function get_class() {
        if (!is_null($this->glider_class)) {
            $this->parent->where[] = 'class = :class';
            $this->parent->parameters['glider_class'] = $this->glider_class;
        }
    }

    public function set_pilot_id($value) {
        if (is_numeric($this->pilot_id)) {
            $this->pilot_id = $value;
        }
    }

    public function get_pilot_id() {
        if (!is_null($this->pilot_id)) {
            $this->parent->where[] = 'p.pid=:pid';
            $this->parent->parameters['pid'] = $this->pilot_id;
        }
    }

    public function get_glider_mode() {
        if ($this->glider_mode) {
            $this->parent->set_glider_view();
        }

    }

    public function get_min_score() {
        $this->parent->where[] = $this->parent->ScoreType . ' > ' . $this->minimum_score;
    }
}
 