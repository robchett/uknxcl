<?php

namespace template\cms;

use classes\ajax;
use classes\get;
use classes\ini;
use classes\interfaces\page_template_interface;
use classes\module;
use core;
use html\node;
use JetBrains\PhpStorm\Pure;
use module\cms\controller;

/**
 * @property controller $module
 */
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

    public function get(): string|array {
        if (!ajax) {
            core::$inline_script[] = 'loaded_modules = {"' . uri . '":true};';
            $attrs = ['data-url' => isset($_POST['url']) ? $_POST['url'] : '/' . uri];
            return "<div id='main'><div id='{$this->get_page_selector()}' {$attrs}>{$this->get_view()}</div>";
        } else {
            $url = (isset($_POST['url']) ? $_POST['url'] : '/' . uri);
            ajax::inject('#main', 'append', "<div id='{$this->get_page_selector()}' data-url='{$url}'>{$this->get_view()}</div>", "#{$this->get_page_selector()}");
            return [];
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
        $css = core::$singleton->get_css();
        return "<head><title>{$this->get_title_tag()}</title><meta name='viewport' content='initial-scale=1.0, user-scalable=no'/>{$css}</head>";
    }

    public function get_title_tag() {
        return ini::get('site', 'title_tag', 'NO Title tag!!!');
    }

    public function get_body(): string {
        $class = core::$page_config->get_body_class();
        return "<body class={$class}>
            {$this->get_nav()}
            {$this->get_pre_content()}
            <div id='#content'>$this->inner_html</div>
            {$this->get_post_content()}
        </body>";
    }

    protected function get_nav(): string {
        return $this->module->get_main_nav();
    }

    protected function get_pre_content(): string {
        return core::$page_config->pre_content;
    }

    protected function get_post_content(): string {
        return core::$page_config->post_content;
    }

    #[Pure]
    public function get_footer(): string {
        return core::$singleton->get_js();
    }
}
