<?php

namespace template\cms;

use classes\ajax;
use classes\attribute_list;
use classes\get;
use classes\interfaces\model_interface;
use classes\interfaces\page_template_interface;
use classes\module;
use classes\push_state;
use classes\table;
use core;
use form\schema;
use html\node;
use module\cms\controller;

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

    public function __construct(public controller $module, public schema $schema, public model_interface|false $current) {
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
            $attrs = new attribute_list(dataUrl: (string) ($_POST['url'] ?? ('/' . uri)));
            return "<div id='main'><div id='{$this->get_page_selector()}' {$attrs}>{$this->get_view()}</div>";
        } else {
            $url = (string) ($_POST['url'] ?? ('/' . uri));
            ajax::inject('#main', 'append', "<div id='{$this->get_page_selector()}' data-url='{$url}'>{$this->get_view()}</div>", "#{$this->get_page_selector()}");
        }
        return '';
    }

    public function get_page_selector(): string {
        return get::__namespace($this->module, 0) . ($this->current instanceof model_interface && $this->current->get_primary_key() ? '-' . $this->current->get_primary_key() : '');
    }

    abstract public function get_view(): string;

    public function get_head(): string {
        $css = core::$singleton->get_css();
        return "<head><title>{$this->get_title_tag()}</title><meta name='viewport' content='initial-scale=1.0, user-scalable=no'/>{$css}</head>";
    }

    public function get_title_tag(): string {
        return 'UKNXCL National Cross Country League';
    }

    public function get_body(): string {
        $class = $this->get_body_class();
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
        return $this->pre_content;
    }

    protected function get_post_content(): string {
        return $this->post_content;
    }

    public function get_footer(): string {
        return core::$singleton->get_js();
    }

    public function get_push_state(): ?push_state
    {
        return null;
    }
}
