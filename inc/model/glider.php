<?php

namespace model;

use classes\ajax;
use form\form;


class glider extends scorable {


    public $gid;
    public string $name;
    public $id;
    public $glider;
    public string $club;
    public int $score = 0;
    public int $total = 0;
    public int $number_of_flights = 0;
    public array $flights = [];
    public $max_flights;
    public int $class = 1;
    public string $output_function = 'table';

    public string $primary_name = 'glider';
    public string $secondary_name = 'club';
    public ?string $tertiary_name = null;
    public $manufacturer;

    /**
     * @param string $flight
     * @param int $num
     */
    function __construct($flight = '', $num = 0) {
        parent::__construct();
        if ($flight != '') {
            /** @var flight $flight */
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
        $field = form::create('field_link', 'gid')->set_attr('link_module', \model\glider::class)->set_attr('link_field', ['manufacturer.title', 'name'])->set_attr('options', ['join' => ['manufacturer' => 'manufacturer.mid = glider.mid'], 'order' => 'manufacturer.title, glider.name']);
        $field->parent_form = $this;
        ajax::update($field->get_html());
    }
}

