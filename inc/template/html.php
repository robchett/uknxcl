<?php

namespace template;

use classes\get;
use classes\icon;
use classes\ini;
use classes\interfaces\page_template_interface;
use classes\module;
use core;
use html\node;
use JetBrains\PhpStorm\Pure;

abstract class html implements page_template_interface {


    protected module $module;
    protected $inner_html;

    public function __construct(module $module) {
        $this->module = $module;
    }

    public function get_page(): string {
        $this->inner_html = $this->get();
        return "<!DOCTYPE html>
<html>
    {$this->get_head()}
    {$this->get_body()}
    {$this->get_footer()}
</html>";
    }


    public function get(): array|string {
        if (!ajax) {
            core::$inline_script[] = 'loaded_modules = {"' . uri . '":true};';
            return node::create('div#left_col', [],
                    node::create('div#nav ul', [], core::$singleton->module->get_main_nav()) .
                    node::create('div#main_wrapper div#main', [],
                        node::create('div#' . $this->get_page_selector(), ['data-uri' => (isset($_POST['url']) ? $_POST['url'] : uri)],
                            $this->get_view()
                        )
                    )
                ) .
                node::create('div#map_interface_3d', [],
                    icon::get('chevron-left', 'span', ['class' => ['show', 'toggle']]) .
                    icon::get('remove', 'span', ['class' => ['hide', 'toggle']]) .
                    node::create('div#tree_content', [],
                        node::create('a.load_airspace.button', ['href' => '#', 'onclick' => 'map.load_airspace();'], 'Load Airspace')
                    )
                ) .
                node::create('div#map_wrapper', [],
                    node::create('div#waypoint_mode_help', [], 'You are in waypoint mode') .
                    node::create('div#map_interface', [],
                        node::create('div#map_interface_padding', [],
                            node::create('div#graph_wrapper', []) .
                            node::create('div#slider', []) .
                            node::create('div#controls', [],
                                node::create('a#play.glyphicon.glyphicon-play', ['onclick' => "map.play()"], 'Play') .
                                node::create('a#pause.glyphicon.glyphicon-pause', ['onclick' => "map.pause()"], 'Pause') .
                                node::create('a#slider_time', [], '00:00')
                            )
                        )
                    ) .
                    node::create('div#map p.loading', [], 'Google Maps are loading...') .
                    node::create('div#map3d p.loading', [], 'Google Earth is loading...')
                );
        } else {
            $content = $this->get_view();
            return ['#main', 'append', '<div id="' . $this->get_page_selector() . '" data-url="' . (isset($_POST['url']) ? $_POST['url'] : '/' . uri) . '">' . $content . '</div>', '#' . $this->get_page_selector()];
        }
    }

    public function get_page_selector(): string {
        return get::__namespace($this->module, 0) . (isset($this->module->current) && $this->module->current->get_primary_key() ? '-' . $this->module->current->get_primary_key() : '');
    }

    /**
     * @return string
     */
    abstract public function get_view(): string;

    public function get_head(): string {
        return node::create('head', [],
            node::create('title', [], $this->get_title_tag()) .
            node::create('meta', ['name' => 'viewport', 'content' => 'initial-scale=1.0, user-scalable=no']) .
            core::$singleton->get_css()
        );
    }

    public function get_title_tag() {
        return ini::get('site', 'title_tag', 'NO Title tag!!!');
    }

    public function get_body(): string {
        return node::create('body.' . core::$page_config->get_body_class(), [],
            core::$page_config->pre_content .
            node::create('div#content', [], $this->inner_html) .
            core::$page_config->post_content
        );
    }

    #[Pure]
    public function get_footer(): string {
        return core::$singleton->get_js();
    }
}
 