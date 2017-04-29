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

    public $primary_name = 'glider';
    public $secondary_name = 'club';
    public $tertiary_name = false;

    /**
     * @param string $flight
     * @param int $num
     * @param int $split
     */
    function __construct($flight = '', $num = 0, $split = 0) {
        parent::__construct();
        if ($flight != '') {
            /** @var \object\flight $flight */
            parent::__construct($flight, $num);
            if ($this->number_of_flights == 1) {
                $this->club = $flight->gm_title;
                $this->name = $flight->g_name;
            }
        }
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

