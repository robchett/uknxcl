<?php
namespace template;

use classes\ajax;
use classes\module;
use html\node;

abstract class html extends \core\template\html {

    public function get() {
        if (!ajax) {
            \core::$inline_script[] = 'loaded_modules = {"' . uri . '":true};';
            $html =
                node::create('div#left_col', [],
                    node::create('div#nav ul', [], \core::$singleton->module->get_main_nav()) .
                    node::create('div#main_wrapper div#main', [],
                        node::create('div#' . $this->get_page_selector(), ['data-uri' => (isset($_POST['url']) ? $_POST['url'] : uri)],
                            $this->get_view()
                        )
                    )
                ) .
                node::create('div#map_wrapper', [],
                    node::create('div#waypoint_mode_help', ['style' => 'display:none'], 'You are in waypoint mode') .
                    node::create('div#map_interface', [],
                        node::create('div#map_interface_padding', [],
                            node::create('div#graph_wrapper', [], '') .
                            node::create('div#slider', [], '') .
                            node::create('div#controls', [],
                                node::create('input#play', ['value' => 'play', 'type' => 'submit', 'onclick' => "map.play()"], '') .
                                node::create('input#pause', ['value' => 'pause', 'type' => 'submit', 'onclick' => "map.pause()"], '') .
                                node::create('a#slider_time', [], '00:00')
                            )
                        )
                    ) .
                    node::create('div#map_interface_3d', [],
                        node::create('span.show', [], 'Show') .
                        node::create('span.hide', [], 'Hide') .
                        node::create('div#tree_content', [],
                            node::create('a.load_airspace.button', ['href' => '#', 'onclick' => 'map.load_airspace();'], 'Load Airspace')
                        )
                    ) .
                    node::create('div#map p.loading', [], 'Google Maps are loading...') .
                    node::create('div#map3d p.loading', [], 'Google Earth is loading...')
                );
            return $html;
        } else {
            $content = $this->get_view();
            return ['#main', 'append', '<div id="' . $this->get_page_selector() . '" data-url="' . (isset($_POST['url']) ? $_POST['url'] : '/' . uri) . '">' . $content . '</div>', '#' . $this->get_page_selector()];
        }
    }
}
 