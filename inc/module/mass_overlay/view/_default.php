<?php

namespace module\mass_overlay\view;

use classes\ajax;
use classes\view;
use core\html\node;
use object\flight;
use traits\twig_view;

/** @property \module\mass_overlay\controller $module */
class _default extends \template\html {
    use twig_view;

    /**
     * @return \html\node
     */
    public function get_template_data() {
        $coordinates = [];
        foreach($this->module->current->get_flights() as $flight) {
            if ($flight->coords) {
                $coordinates[$flight->get_primary_key()] = $flight->coords;
            }
        }
        return [
            'flights' => $coordinates,
            'table' => $this->module->current->get_table()
        ];
    }
}