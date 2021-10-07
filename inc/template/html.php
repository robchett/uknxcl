<?php

namespace template;

use classes\ajax;
use classes\get;
use classes\icon;
use classes\interfaces\model_interface;
use classes\interfaces\page_template_interface;
use classes\module;
use classes\push_state;
use classes\table;
use core;
use html\node;

/** 
 * @template T of module 
 * @template S
 */
abstract class html implements page_template_interface {

    protected string $inner_html;
    public string $pre_content = '';
    public string $post_content = '';
    /** @var string[] */
    public array $body_classes = [];
    public string $title_tag;

    public function get_body_class(): string {
        return implode('.', $this->body_classes);
    }

    public function add_body_class(...$classes): void { 
        $this->body_classes = array_merge($this->body_classes, $classes);
    }

    /** 
     * @param T $module
     * @param S $current
     */
    public function __construct(public module $module, public mixed $current) {
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


    public function get(): string {
        if (!ajax) {
            core::$inline_script[] = 'loaded_modules = {"' . uri . '":true};';
            return node::create('div#left_col', [],
                    node::create('div#nav ul', [], core::$singleton->module->get_main_nav()) .
                    node::create('div#main_wrapper div#main', [],
                        node::create('div#' . $this->get_page_selector(), ['dataUrl' => ($_POST['url'] ?? uri)],
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
            ajax::inject('#main', 'append', '<div id="' . $this->get_page_selector() . '" data-url="' . (string) ($_POST['url'] ?? '/' . uri) . '">' . $content . '</div>', '#' . $this->get_page_selector());
        }
        return '';
    }

    public function get_page_selector(): string {
        return get::__namespace($this->module, 0) . ($this->current instanceof model_interface && $this->current->get_primary_key() ? '-' . $this->current->get_primary_key() : '');
    }

    abstract public function get_view(): string;

    public function get_head(): string {
        return node::create('head', [],
            node::create('title', [], $this->get_title_tag()) .
            node::create('meta', ['name' => 'viewport', 'content' => 'initial-scale=1.0, user-scalable=no']) .
            core::$singleton->get_css()
        );
    }

    public function get_title_tag(): string {
        return 'UKNXCL National Cross Country League';
    }

    public function get_body(): string {
        return node::create('body.' . $this->get_body_class(), [],
            $this->pre_content .
            node::create('div#content', [], $this->inner_html) .
            $this->post_content
        );
    }

    public function get_footer(): string {
        return core::$singleton->get_js();
    }

    public function get_push_state(): ?push_state {
        $push_state = new push_state();
        $module = get::__namespace($this->module, 0);
        $view = get::__basename($this);
        $push_state->url = ($this->current instanceof model_interface ? $this->current->get_url() : '/' . $module . ( $view != '_default' ? '/' . $view : ''));
        if ($this->module->page > 1) {
            $push_state->url .= 'page/' . $this->module->page;
        }
        $push_state->id = '#' . $this->get_page_selector();
        return $push_state;
    }
}
 