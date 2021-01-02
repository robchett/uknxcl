<?php

namespace module\mass_overlay\view;

use module\mass_overlay\controller;
use template\html;

/** @property controller $module */
class _default extends html {

    public function get_view(): string {
        $table = $this->module->current;
        $coordinates = '';
        foreach ($table->get_flights() as $flight) {
            if ($flight->coords) {
                $coordinates .= "map.add_flight_coordinates('{$flight->coords}', {$flight->get_primary_key()}, false);";
            }
        }
        return "
        <h2>The following flights are displayed on the map</h2>
<div id='generated_tables'>{$table->get_table()}</div>

<script>
    var map = map || [];
    map.push(function () {
        map.clear();
        $coordinates
        var bound = new google.maps.LatLngBounds(new google.maps.LatLng(57.326521, -6.551285), new google.maps.LatLng(50.875311, 2.127914));
        map.internal_map.fitBounds(bound);
    });
</script>";
    }
}