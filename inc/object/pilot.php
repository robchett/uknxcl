<?php
class pilot extends table {
    public $name;
    public $id;
    public $glider;
    public $club;
    public $score = 0;
    public $total = 0;
    public $number_of_flights = 0;
    public $flights = array();
    public $max_flights;
    public $class = 1;
    public $output_function = 'table';
    public static $module_id= 3;

    public $table_key = 'pid';
/*    public static $fields = array(
        array('field_string', 'name'),
        array('field_string', 'bhpa_no'),
        array('field_select', 'rating'),
        array('field_select', 'gender'),
        array('field_string', 'email')
    );*/

    public function set_from_flight(flight $flight, $num = 6, $split = false) {
        $this->max_flights = $num;
        $this->club = $flight->c_name;
        $this->glider = $flight->g_name;
        $this->score += $flight->score;
        $this->total += $flight->score;
        $this->number_of_flights++;
        $this->flights[] = $flight->to_print()->get();
        if ($split == 1)
            $this->class = $flight->class;
        else
            $this->class = 1;
        $this->id = $flight->ClassID;
        $this->name = $flight->p_name;
    }

    public function add_flight(flight $flight) {
        if (count($this->flights) < $this->max_flights) {
            $this->score += $flight->score;
            $this->flights[] = $flight->to_print()->get();
        }
        $this->total += $flight->score;
        $this->number_of_flights++;
    }

    public function output($pos) {
        return $this->{'output_' . $this->output_function}($pos);
    }

    public function output_table($pos) {
        $flights = implode('', $this->flights);
        for ($i = count($this->flights); $i < $this->max_flights; $i++) {
            $flights .= '<td/>';
        }
        return '
<tr class="class' . $this->class . '">
    <td>' . $pos . '</td>
    <td>' . $this->name . '</td>
    <td>' . $this->glider . '<br/>' . $this->club . '</td>
    ' . $flights . '
    <td>' . $this->score . ($this->score == $this->total ? '' : '<br/>' . $this->total) . ' (' . $this->number_of_flights . ')</td>
</tr>' . "\n";
    }

    public function output_csv($pos) {
        $csv = "$pos','$this->name','$this->glider / $this->club',$this->flights";
        for ($i = $this->number_of_flights; $i < $this->max_flights - 1; $i++) {
            $csv .= "'',";
        }
        $csv .= "$this->score,$this->total ($this->number_of_flights),\n\r";
        return $csv;
    }

    public function do_update_selector() {
        $field = form::create('field_select', 'pid')
            ->set_attr('options', alphabeticalise::pilot_array());
        ajax::update($field->get_html());
    }
    public static function get_all($fields = array(), $options = array()){
        return pilot_array::get_all($fields, $options);
    }
}

class pilot_array extends table_array {
    /* @return pilot */
    public function next() {
        return parent::next();
    }
}

class pilot_iterator extends table_iterator {

    /* @return pilot */
    public function key() {
        return parent::key();
    }
}

?>