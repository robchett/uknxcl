<?php

namespace model;

use classes\ajax;
use classes\table;
use form\form;
use JetBrains\PhpStorm\Pure;

class scorable extends table {

    public $bhpa_no;
    public int $class = 1;
    public string $club;
    public $email;
    public array $flights = [];
    public $gender;
    public $glider;
    public $id;
    public $max_flights;
    public string $name;
    public int $number_of_flights = 0;
    public string $output_function = 'table';
    public $pid;
    public $rating;
    public int $score = 0;
    public int $total = 0;

    public string $primary_name = 'name';
    public string $secondary_name = 'glider';
    public ?string $tertiary_name = 'club';

    /**
     * @param flight $flight
     */
    public function add_flight(flight $flight) {
        if (count($this->flights) < $this->max_flights) {
            $this->score += $flight->score;
            $this->flights[] = (string)$flight->to_print();
        }
        $this->total += $flight->score;
        $this->number_of_flights++;
    }

    /**
     *
     */
    public function do_update_selector() {
        $field = form::create('field_link', 'pid')->set_attr('link_module', \model\pilot::class)->set_attr('link_field', 'name')->set_attr('options', ['order' => 'name']);
        $field->parent_form = $this;
        ajax::update($field->get_html());
    }

    /**
     * @param $pos
     * @return string
     */
    public function output($pos): string {
        return $this->{'output_' . $this->output_function}($pos);
    }

    /**
     * @param $pos
     * @return string
     */
    #[Pure]
    public function output_csv($pos): string {
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
    #[Pure]
    public function output_table($pos): string {
        $flights = implode('', $this->flights);
        for ($i = count($this->flights); $i < $this->max_flights; $i++) {
            $flights .= '<td class="left"></td>';
        }
        return '
<tr class="class' . $this->class . '">
    <td class="left">' . $pos . '</td>
    <td class="left">' . $this->{$this->primary_name} . '</td>
    <td class="left">' . $this->{$this->secondary_name} . ($this->tertiary_name ? '<br/>' . $this->{$this->tertiary_name} : '') . '</td>
    ' . $flights . '
    <td class="left">' . $this->score . ($this->score == $this->total ? '' : '<br/>' . $this->total) . ' (' . $this->number_of_flights . ')</td>
</tr>';
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
        $this->flights[] = (string)$flight->to_print();
        if ($split)
            $this->class = $flight->class; else
            $this->class = 1;
        $this->id = $flight->ClassID;
        $this->name = $flight->p_name;
    }
}

