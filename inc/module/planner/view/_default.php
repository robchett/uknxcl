<?php
namespace module\planner\view;

use module\planner\form;
use module\planner\object;
use object\flight_type;
use traits\twig_view;

class _default extends \template\html {
    use twig_view;

    public function get_template_data() {
        $form = new form\planner_load_waypoints();
        return [
            'form' =>$form->get_html(),
            'url_string' => $this->module->import_string,
            'flight_type' => [
                'GO_ID' => flight_type::GO_ID,
                'OR_ID' => flight_type::OR_ID,
                'FT_ID' => flight_type::FT_ID,
            ]
        ];
    }
}
