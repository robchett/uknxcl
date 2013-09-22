<?php
class manufacturer extends club {
    public static $module_id = 5;
    public $table_key = 'mid';
    public $Name;
    public $Score = 0;
    public $Total = 0;
    public $Number = 1;
    public $Glider;
    public $Num;
    public $title;

    public static function get_all(array $fields, array $options = array()) {
        return manufacturer_array::get_all($fields, $options);
    }

    public function set_from_pilot($glider, $num = 6) {
        $this->max_pilots = $num;
        $this->name = $glider->club;
        $this->score = $glider->score;
        $this->total = $this->score;
        $this->content = $glider->output(1);
    }

    function writeClubSemiHead($pos) {
        return '
            <div class="table_wrapper inner"><h3>
            <span class="pos">' . $pos . '</span>
            <span class="score">' . $this->score . '</span>
            <span class="name">' . $this->name . '</span>
            </h3>';
    }

}

class manufacturer_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'manufacturer_iterator');
        $this->iterator = new manufacturer_iterator($input);
    }

    /* @return manufacturer */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class manufacturer_iterator extends table_iterator {

    /* @return manufacturer */
    public function key() {
        return parent::key();
    }
}