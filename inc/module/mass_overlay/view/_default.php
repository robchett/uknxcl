<?php

namespace module\mass_overlay\view;

use classes\ajax;
use classes\view;
use core\html\node;
use object\flight;

/** @property \module\mass_overlay\controller $module */
class _default extends \template\html {

    /**
     * @return \html\node
     */
    public function get_view() {
        $this->module->current->get_flights();
        $script = 'map.clear();';
        $this->module->current->flights->iterate(function (flight $flight) use (&$script) {
            if ($flight->coords != '?') {
                $script .= 'map.add_flight_coordinates("' . str_replace("\n", '', $flight->coords) . '", ' . $flight->get_primary_key() . ');';
            }
        });
        $script .= '
            var bound = new google.maps.LatLngBounds(new google.maps.LatLng(57.326521,-6.551285), new google.maps.LatLng(50.875311,2.127914));
            map.internal_map.fitBounds(bound);
        ';
        if(ajax) {
            ajax::add_script($script);
        } else {
            \core::$inline_script[] = $script;
        }
        return node::create('h2',[], 'The following flights are displayed on the map') . node::create('div#generated_tables', [], $this->module->current->get_table());
    }
}