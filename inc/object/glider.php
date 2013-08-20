<?php

class glider extends pilot {
    public static $module_id = 4;
    public $gid;
    public $name;
    public $id;
    public $glider;
    public $club;
    public $score = 0;
    public $total = 0;
    public $number_of_flights = 0;
    public $flights;
    public $max_flights;
    public $class = 1;
    public $output_function = 'table';
    public $table_key = 'gid';

    function __construct($flight = '', $num = 0, $split = 0) {
        parent::__construct();
        if ($flight != '') {
            parent::__construct($flight, $num, $split);
            if ($this->number_of_flights == 1) {
                $this->club = $flight->gm_title;
                $this->name = $flight->g_name;
            }
        }
    }

    public static function get_all($fields = array(), $options = array()) {
        return glider_array::get_all($fields, $options);
    }

    public function output_table($pos) {
        $flights = implode('', $this->flights);
        for ($i = count($this->flights); $i < $this->max_flights; $i++) {
            $flights .= '<td></td>';
        }
        return '
<tr class="class' . $this->class . '">
    <td>' . $pos . '</td>
    <td>' . $this->glider . '</td>
    <td>' . $this->club . '</td>
    ' . $flights . '
    <td>' . $this->score . ($this->score == $this->total ? '' : '<br/>' . $this->total) . ' (' . $this->number_of_flights . ')</td>
</tr>' . "\n";
    }

    public function do_update_selector() {
        $field = form::create('field_link', 'gid')
            ->set_attr('link_module', 'glider')
            ->set_attr('link_field', array('manufacturer.title', 'glider.name'))
            ->set_attr('options', array('join' => array('manufacturer' => 'manufacturer.mid = glider.mid'), 'order' => 'manufacturer.title, glider.name'));
        $field->parent_form = $this;
        ajax::update($field->get_html());
    }
}

class glider_array extends table_array {
    /* @return pilot */
    public function next() {
        return parent::next();
    }
}

class glider_iterator extends table_iterator {

    /* @return pilot */
    public function key() {
        return parent::key();
    }
}
