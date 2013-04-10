<?php
class manufacturer extends table {
    public static $module_id = 5;
    public $table_key = 'mid';
    public $Name;
    public $Score = 0;
    public $Total = 0;
    public $Number = 1;
    public $Glider;
    public $Num;

    /* @return manufacturer_array */
    public static function get_all(array $fields, array $options = array()) {
        return manufacturer_array::get_all($fields, $options);
    }

    public function AddSub($glider, $flights) {
        if ($this->Number < $this->Num) {
            $this->Score += $glider->Score;
            $this->Number++;
            $this->Glider .= $glider->Output($this->Number, $flights);
        }
    }

    public function set_from_pilot(glider $glider, $num = 6, $flights) {
        $this->Num = $num;
        $this->Name = $glider->Manu;
        $this->Score = $glider->Score;
        $this->Total = $this->Score;
        $this->Glider = $glider->Output(1, $flights);
    }

    function writeClubSemiHead($pos) {
        $a = "
            <table class='Title'><th style=\"width:50px\">$pos</th>
            <th  style=\"width:90px\">$this->Score</th>
            <th  style=\"width:538px\">$this->Name</th>
            </table>";
        return $a;
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