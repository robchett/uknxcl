<?php

class flight_view extends view {
    /** @var  flight_info */
    public $module;

    public function get_view() {
        $html = '<div class="flight_wrapper">';
        $html .= '<h1>' . $this->module->current->pilot_name . ' <span>' . $this->module->current->date . '</span></h1>';
        $html .= $this->module->current->get_info();

        if ($this->module->current->did > 1) {
            $html .= $this->module->current->get_stats();
            $html .= '<div id="graph-' . $this->module->current->fid . '" class="graph"></div>';
            core::$inline_script[] = '
var graph = new Graph($("#graph-' . $this->module->current->fid . '"));
var flight = new Track(' . $this->module->current->fid . ');
flight.add_nxcl_data(function() {graph.swap(flight)});
            map.callback = function() {this.add_flight(' . $this->module->current->fid . ')};';
        }
        $html .= '</div>';
        return $html;
    }
}
