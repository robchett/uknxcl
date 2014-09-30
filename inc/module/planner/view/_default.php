<?php
namespace module\planner\view;

use classes\ajax;
use classes\view;
use html\node;
use module\planner\form;
use module\planner\object;
use object\flight_type;

class _default extends \template\html {

    public function get_view() {
        $form = new form\planner_load_waypoints();
        $form->h2 = 'Load a waypoint set';
        $html = node::create('div#waypoints', [],
            node::create('h3.heading', [], 'Flight Planner') .
            node::create('div#wp_overlay a#enter_wp_mode.button', [], 'Enter Planner mode') .
            node::create('ul', [],
                node::create('li', [], 'Click the map to add a waypoint or load the a predefined set of waypoints: ') .
                $form->get_html() .
                node::create('li', [], 'Click waypoints to add them to your flight path')

            ) .
            node::create('ul#flight_types', [],
                node::create('li', [], 'An open distance flight of 5 or less points can be saved as a declaration of intent by clicking ' . node::create('a#decOD.button.inline', ['data-ajax-click' => 'planner:get_form', 'data-ajax-post' => '{"coordinates":"", "ftid":' . flight_type::GO_ID . '}', 'disabled' => 'disabled'], 'here')) .
                node::create('li', [], 'An out and return of 3 points where the 3rd is also the 1st can be saved as a declaration of intent by clicking ' . node::create('a#decOR.button.inline', ['data-ajax-click' => 'planner:get_form', 'data-ajax-post' => '{"coordinates":"", "ftid":' . flight_type::OR_ID . '}', 'disabled' => 'disabled'], 'here')) .
                node::create('li', [], 'A triangle of 4 points where the 4th is also the 1st can be saved as a declaration of intent by clicking ' . node::create('a#decTR.button.inline', ['data-ajax-click' => 'planner:get_form', 'data-ajax-post' => '{"coordinates":"", "ftid":' . flight_type::TR_ID . '}', 'disabled' => 'disabled'], 'here'))
            ) .
            node::create('div#path_wrapper', [],
                node::create('h4.heading.joined', [], 'Path') .
                node::create('div#path', [], '')
            ) .
            node::create('p', [], node::create('a#leave_wp_mode.button', [], 'Leave Waypoint mode (clears map of markers a well)'))
        );


        $script = '$("a#enter_wp_mode").click(function(){map.planner.enable();});';
        $script .= '$("a#leave_wp_mode").click(function(){map.planner.clear();});';
        if (ajax) {
            $script .= 'map.planner.calculate_distances();map.planner.writeplanner();';
            ajax::add_script($script);
        } else {
            if($this->module->import_string) {
                \core::$global_script[] = 'var planner_string = "' . $this->module->import_string . '"';
            }
            \core::$inline_script[] = $script;
        }

        return $html->get();
    }
}
