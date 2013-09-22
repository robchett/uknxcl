<?php
class pilot extends table {
    use table_trait;

    public static $module_id = 3;
    public $bhpa_no;
    public $class = 1;
    public $club;
    public $email;
    public $flights = array();
    public $glider;
    public $id;
    public $max_flights;
    public $name;
    public $number_of_flights = 0;
    public $output_function = 'table';
    public $pid;
    public $score = 0;
    public $table_key = 'pid';
    public $total = 0;

    /*    public static $fields = array(
            array('field_string', 'name'),
            array('field_string', 'bhpa_no'),
            array('field_select', 'rating'),
            array('field_select', 'gender'),
            array('field_string', 'email')
        );*/

    /**
     * @param array $fields
     * @param array $options
     * @return pilot_array
     */
    public static function get_all($fields = array(), $options = array()) {
        return pilot_array::get_all($fields, $options);
    }

    /**
     * @param flight $flight
     */
    public function add_flight(flight $flight) {
        if (count($this->flights) < $this->max_flights) {
            $this->score += $flight->score;
            $this->flights[] = $flight->to_print()->get();
        }
        $this->total += $flight->score;
        $this->number_of_flights++;
    }

    /**
     *
     */
    public function do_update_selector() {
        $field = form\form::create('field_link', 'pid')
            ->set_attr('link_module', 'pilot')
            ->set_attr('link_field', 'name')
            ->set_attr('options', array('order' => 'name'));
        $field->parent_form = $this;
        \ajax::update($field->get_html());
    }

    /**
     * @param $pos
     * @return mixed
     */
    public function output($pos) {
        return $this->{'output_' . $this->output_function}($pos);
    }

    /**
     * @param $pos
     * @return string
     */
    public function output_csv($pos) {
        $csv = $pos . ',\'' . $this->name . '\',\'' . $this->glider . '/' . $this->club . '\',' . strip_tags(implode(',', $this->flights));
        for ($i = $this->number_of_flights; $i < $this->max_flights - 1; $i++) {
            $csv .= ',';
        }
        $csv .= $this->score . ',' . $this->total . '(' . $this->number_of_flights . ')<br/>';
        return $csv;
    }

    /**
     * @param $pos
     * @return string
     */
    public function output_table($pos) {
        $flights = implode('', $this->flights);
        for ($i = count($this->flights); $i < $this->max_flights; $i++) {
            $flights .= '<td></td>';
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

    /**
     * @param flight $flight
     * @param int $num
     * @param bool $split
     */
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
}

/**
 * Class pilot_array
 */
class pilot_array extends table_array {
    /* @return pilot */
    public function next() {
        return parent::next();
    }
}

/**
 * Class pilot_iterator
 */
class pilot_iterator extends table_iterator {

    /* @return pilot */
    public function key() {
        return parent::key();
    }
}

