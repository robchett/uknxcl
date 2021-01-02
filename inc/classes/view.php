<?php

namespace classes;

use core;
use html\node;

abstract class view {

    public $module;

    public function get(): string {
        if (!ajax) {
            core::$inline_script[] = 'loaded_modules = {"' . uri . '":true};';
            $attrs = attribute_list::create(['data-url' => isset($_POST['url']) ? $_POST['url'] : '/' . uri]);
            return "<div id='main'><div id='{$this->get_page_selector()}' $attrs>{$this->get_view()}</div>";
        } else {
            $this->get_view_ajax();
        }
        return '';
    }

    public function get_page_selector(): string {
        return get::__namespace($this->module, 0) . (isset($this->module->current) && $this->module->current->get_primary_key() ? '-' . $this->module->current->get_primary_key() : '');
    }

    /**
     * @return string
     */
    abstract public function get_view(): string;

    public function get_view_ajax() {
        $content = $this->get_view();
        ajax::inject('#main', 'append', '<div id="' . $this->get_page_selector() . '" data-url="' . (isset($_POST['url']) ? $_POST['url'] : uri) . '">' . $content . '</div>', '#' . $this->get_page_selector());
    }

    public function get_ajax() {

    }
}