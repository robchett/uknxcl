<?php

class _default_view extends view {

    public function get_view() {
        $html = html_node::create('div#waypoints')
            ->nest(array(
                    html_node::create('h3', 'Flight Planner'),
                    html_node::create('a#enter_wp_mode.button', 'Enter Planner mode'),
                    html_node::create('ul')->nest(array(
                            html_node::create('li', 'Click the map to add a waypoint or ' . html_node::inline('a.button.inline', 'load the BOS waypoints', array('data-ajax-click' => 'planner:do_load_bos'))),
                            html_node::create('li', 'Click waypoints to add them to your flight path'),
                        )
                    ),
                    html_node::create('ul#flight_types')->nest(array(
                            html_node::create('li', 'An open distance flight of 5 or less points can be saved as a declaration of intent by clicking ' . html_node::inline('a#decOD.button.inline', 'here', array('data-ajax-click' => 'planner:get_form', 'data-ajax-post' => '{"coordinates":"", "ftid":2}', 'disabled' => 'disabled'))),
                            html_node::create('li', 'An out and return of 3 points where the 3rd is also the 1st can be saved as a declaration of intent by clicking ' . html_node::inline('a#decOR.button.inline', 'here', array('data-ajax-click' => 'planner:get_form', 'data-ajax-post' => '{"coordinates":"", "ftid":3}', 'disabled' => 'disabled'))),
                            html_node::create('li', 'A triangle of 4 points where the 4th is also the 1st can be saved as a declaration of intent by clicking ' . html_node::inline('a#decTR.button.inline', 'here', array('data-ajax-click' => 'planner:get_form', 'data-ajax-post' => '{"coordinates":"", "ftid":4}', 'disabled' => 'disabled')))
                        )
                    ),
                    html_node::create('div#path_wrapper', '')->nest(array(
                            html_node::create('h4', 'Path'),
                            html_node::create('div#path', '')
                        )
                    ),
                    html_node::create('p', html_node::inline('a#leave_wp_mode.button', 'Leave Waypoint mode (clears map of markers a well)')),
                )
            );

        $script = '$("a#enter_wp_mode").click(function(){map.planner.enable();});';
        $script .= '$("a#leave_wp_mode").click(function(){map.planner.clear();});';
        if (ajax) {
            ajax::add_script($script);
        } else {
            core::$inline_script[] = $script;
        }

        return $html->get();
    }
}
