<?php
class club extends table {
    public static $module_id = 12;
    public $table_key = 'cid';
    public $name;
    public $score = 0;
    public $total = 0;
    public $number = 1;
    public $content;
    public $max_pilots;

    /* @return club_array */
    public static function get_all(array $fields, array $options = array()) {
        return club_array::get_all($fields, $options);
    }

    public function AddSub($pilot, $flights) {
        if ($this->number < $this->max_pilots) {
            $this->score += $pilot->score;
            $this->number++;
            $this->content .= $pilot->output($this->number);
        }
    }

    function set_from_pilot($pilot, $num, $flights) {
        $this->max_pilots = $num;
        $this->name = $pilot->club;
        $this->score = $pilot->score;
        $this->total = $this->score;
        $this->content = $pilot->output(1);
    }

    function writeClubSemiHead($pos) {
        $a = '
            <div class="table_wrapper inner"><h3>
            <span class="pos">' . $pos . '</span>
            <span class="score">' . $this->score . '</span>
            <span class="name">' . $this->name . '</span>
            </h3>';
        return $a;
    }
}

class club_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'club_iterator');
        $this->iterator = new club_iterator($input);
    }

    /* @return club */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class club_iterator extends table_iterator {

    /* @return club */
    public function key() {
        return parent::key();
    }
}