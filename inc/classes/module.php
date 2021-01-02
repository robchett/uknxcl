<?php

namespace classes;

use classes\ajax as _ajax;
use classes\get as _get;
use classes\interfaces\model_interface;
use classes\interfaces\page_template_interface;
use core;
use html\node;
use module\pages\model\page;
use template\html;

class module {

    public static array $page_fields_to_retrieve = ['pid', 'body', 'title'];
    /** @var table */
    public model_interface $current;
    public string $view = '_default';
    /** @var int $page Set by core. */
    public int $page = 1;
    public int $npp = 50;
    /** @var  html */
    public page_template_interface $view_object;
    /** @var  page */
    public page $page_object;

    public function __controller(array $path) {
        if (count($path) > 3 && $path[count($path) - 2] == 'page') {
            if (end($path) == 'all') {
                $this->npp = 99999999;
                $this->page = 1;
            } else {
                $this->page = end($path);
            }
        }
        $this->set_view();
        if (ini::get('mysql', 'database', false)) {
            $this->set_page();
        }
        core::$page_config->add_body_class('module_' . _get::__namespace($this, 0), $this->view);
    }

    public function set_view() {
        $class = _get::__namespace($this) . '\\view\\' . $this->view;
        if (class_exists($class)) {
            $this->view_object = new $class($this);
        }
    }

    function set_page() {
        $this->page_object = new page();
        if (!isset($this->pid)) {
            $this->page_object->do_retrieve(self::$page_fields_to_retrieve, ['where_equals' => ['module_name' => _get::__namespace($this, 0)]]);
        } else {
            $this->page_object->do_retrieve_from_id(self::$page_fields_to_retrieve, $this->pid);
        }
    }

    function get_main_nav(): string {
        $pages = page::get_all([], ['where' => 'nav=1', 'order' => 'position']);
        return $pages->iterate_return(fn (page $page) => strtr("<li class='%class'><a href='{$page->get_url()}'>%title</a></li>", [
            '%class' => $page->pid == core::$singleton->pid ? '.sel' : '',
            '%title' => $page->nav_title ?: $page->title
        ]));
    }

    public function ajax_load() {
        $this->set_view();
        $this->set_page();
        $this->view_object->get();
        $push_state = $this->get_push_state();
        _ajax::push_state($push_state);
    }

    public function get_push_state(): ?push_state {
        $push_state = new push_state();
        $push_state->url = isset($this->current) ? $this->current->get_url() : '/' . _get::__namespace($this, 0) . ($this->view != '_default' ? '/' . $this->view : '');
        if ($this->page > 1) {
            $push_state->url .= 'page/' . $this->page;
        }
        $push_state->title = $this->page_object->title;
        $push_state->data->url = $push_state->url;
        $push_state->data->module = get_class($this);
        $push_state->data->act = isset($_REQUEST['ajax_act']) ? $_REQUEST['ajax_act'] : 'ajax_load';
        $push_state->data->request = $_REQUEST;
        $push_state->data->id = '#' . $this->view_object->get_page_selector();
        $push_state->push = !isset($_REQUEST['is_popped']) ? true : !$_REQUEST['is_popped'];
        return $push_state;
    }
}
