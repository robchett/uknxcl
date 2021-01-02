<?php

namespace module\planner\view;

use classes\attribute_callable;
use model\flight_type;
use module\planner\controller;
use module\planner\form;
use template\html;

class _default extends html {

    public function get_view(): string {
        $form = new form\planner_load_waypoints();
        $GO_ID = flight_type::GO_ID;
        $OR_ID = flight_type::OR_ID;
        $TR_ID = flight_type::TR_ID;

        $callable = attribute_callable::create([controller::class, 'get_form']);
        return <<<HTML
<h1 class='page-header'>Flight Planner</h1>
<div id='waypoints'>
    <div id='wp_overlay'>
        <a id='enter_wp_mode' class='button'>Enter Planner mode</a>
    </div>
    <p>Click the map to add a waypoint or load the a predefined set of waypoints:</p>
    <div class='callout callout-primary'>
        <h3>Load a waypoint set</h3>
        {$form->get_html()}
    </div>
    <p>Click waypoints to add them to your flight path</p>
    <ul id='flight_types'>
        <li>
            An open distance flight of 5 or less points can be saved as a declaration of intent by clicking
            <a id='decOD' class='button inline' data-ajax-click='$callable' data-ajax-post='{"coordinates": "", "ftid":{$GO_ID}}' disabled>Here</a>
        </li>
        <li>
            An out and return of 3 points where the 3rd is also the 1st can be saved as a declaration of intent by clicking
            <a id='decOR' class='button inline' data-ajax-click='$callable' data-ajax-post='{"coordinates": "", "ftid":{$OR_ID}}' disabled>Here</a></li>
        <li>
            A triangle of 4 points where the 4th is also the 1st can be saved as a declaration of intent by clicking
            <a id='decTR' class='button inline' data-ajax-click='$callable' data-ajax-post='{"coordinates": "", "ftid":{$TR_ID}}' disabled>Here</a>
        </li>
    </ul>
    <div id='path_wrapper'>
        <h4 class='page-header'>Path</h4>
        <div id='path'></div>
    </div>
    <p><a id='leave_wp_mode' class='button'>Leave Waypoint mode (clears map of markers a well)</a></p>
</div>

<script>
var load_callback = load_callback || [];
load_callback.push(function () {
    $('a#enter_wp_mode').click(function () {
        map.planner.enable();
    });
    $('a#leave_wp_mode').click(function () {
        map.planner.clear();
    });
});
var map = map || [];
map.push( function () {
    map.planner.calculate_distances();
    map.planner.writeplanner();
    map.planner.load_string('{$this->module->import_string}');
});
</script>
HTML;
    }
}
