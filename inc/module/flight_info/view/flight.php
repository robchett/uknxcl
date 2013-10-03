<?php
namespace module\flight_info\view;

use classes\view;
use html\node;

class flight extends view {

    /** @var  \module\flight_info\controller */
    public $module;

    public function get_view() {
        $html = node::create('div.flight_wrapper', [],
            node::create('h1', [], $this->module->current->pilot_name . ' ' . node::create('span', [], $this->module->current->date)) .
            $this->module->current->get_info() .
            ($this->module->current->did > 1 ?
                $this->module->current->get_stats() . node::create('div#graph-' . $this->module->current->fid . '.graph') : '')

        );
        if ($this->module->current->did > 1) {
            \core::$inline_script[] = '
            var graph = new Graph($("#graph-' . $this->module->current->fid . '"));
            var flight = new Track(' . $this->module->current->fid . ');
            flight.add_nxcl_data(function() {graph.swap(flight)});
            map.callback = function() {this.add_flight(' . $this->module->current->fid . ')};';
        }
        return $html;
    }
}
