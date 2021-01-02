<?php

namespace module\flight_info\view;

use classes\module;
use module\flight_info\controller;
use template\html;

class flight extends html {

    /** @var controller */
    public module $module;

    function get_view(): string {
        $flight = $this->module->current;
        $res = "
<div class='flight_wrapper'>
    <h1>{$flight->pilot->name} <span>{$flight->format_date($flight->date)}</span></h1>
    {$flight->get_info()}
</div>";
        if ($flight->did > 1) {
            $res .= "
<script>
        var load_callback = load_callback || [];
        load_callback.push(function() {
            var graph = new Graph($('#graph-{$flight->get_primary_key()}'));
            var flight = new Track({$flight->get_primary_key()})
            flight.add_nxcl_data(function() {graph.swap(flight)});
            map.callback(function(map) {map.add_flight({$flight->get_primary_key()})})
        });
</script>";
        }
        return $res;
    }
}
