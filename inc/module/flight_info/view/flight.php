<?php
namespace module\flight_info\view;

use classes\view;
use html\node;
use traits\twig_view;

class flight extends \template\html {
    use twig_view;

    /** @var  \module\flight_info\controller */
    public $module;

    function get_template_file() {
        return 'inc/module/flight_info/view/flight.twig';
    }

    function get_template_data() {
        return [
            'flight' => $this->module->current->get_template_data(),
            'pilot' => $this->module->current->pilot->get_template_data(),
            'flight_info' => $this->module->current->get_info(),
        ];
    }
}
