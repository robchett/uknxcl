<?php

namespace object;

use classes\ajax;
use form\form;
use traits\table_trait;

class glider extends scorable {

    use table_trait;

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

    /**
     * @param string $flight
     * @param int $num
     * @param int $split
     */
    function __construct($flight = '', $num = 0, $split = 0) {
        parent::__construct();
        if ($flight != '') {
            /** @var \object\flight $flight */
            parent::__construct($flight, $num, $split);
            if ($this->number_of_flights == 1) {
                $this->club = $flight->gm_title;
                $this->name = $flight->g_name;
            }
        }
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
    <td>' . $this->glider . '</td>
    <td>' . $this->club . '</td>
    ' . $flights . '
    <td>' . $this->score . ($this->score == $this->total ? '' : '<br/>' . $this->total) . ' (' . $this->number_of_flights . ')</td>
</tr>' . "\n";
    }

    /**
     *
     */
    public function do_update_selector() {
        $field = form::create('field_link', 'gid')
            ->set_attr('link_module', '\\object\\glider')
            ->set_attr('link_field', ['manufacturer.title', 'name'])
            ->set_attr('options', ['join' => ['manufacturer' => 'manufacturer.mid = glider.mid'], 'order' => 'manufacturer.title, glider.name']);
        $field->parent_form = $this;
        ajax::update($field->get_html());
    }
}

