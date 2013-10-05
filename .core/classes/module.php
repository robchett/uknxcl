<?php

namespace core\classes;

use classes\ajax as _ajax;
use classes\get as _get;
use classes\push_state;
use html\node;
use module\pages\object\page;

abstract class module {

    public static $page_fields_to_retrieve = array('pid', 'body', 'title');
    /** @var table */
    public $current;
    public $view = '_default';
    public $page = 1;
    public $npp = 50;
    /** @var  \classes\view */
    public $view_object;
    /** @var  page */
    public $page_object;

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

        try {
            _get::ini('database', 'mysql');
            $this->set_page();
        } catch (\Exception $e) {

        }
        \core::$page_config->add_body_class('module_' . _get::__namespace($this, 0), $this->view);
    }

    function set_page() {
        $this->page_object = new page();
        if (!isset($this->pid)) {
            $this->page_object->do_retrieve(self::$page_fields_to_retrieve, array('where_equals' => array('module_name' => _get::__namespace($this, 0))));
        } else {
            $this->page_object->do_retrieve_from_id(self::$page_fields_to_retrieve, $this->pid);
        }
    }

    function get_main_nav() {
        $pages = page::get_all(array(), array('where' => 'nav=1'));
        return $pages->iterate_return(
            function (page $page) {
                return node::create('li' . ($page->pid == \core::$singleton->pid ? '.sel' : ''), [],
                    node::create('a', ['href' => $page->get_url()], $page->nav_title)
                );
            }
        );
    }

    public function set_view() {
        $class = _get::__namespace($this) . '\\view\\' . $this->view;
        if (class_exists($class)) {
            $this->view_object = new $class;
            $this->view_object->module = $this;
        } else {
            if (dev) {
                throw new \Exception('View not found, ' . $class);
            } else {

            }
        }
    }

    public function ajax_load() {
        $this->set_view();
        $this->set_page();
        $this->view_object->get_view_ajax();
        $push_state = $this->get_push_state();
        _ajax::push_state($push_state);
    }

    public function get_push_state() {
        $push_state = new push_state();
        $push_state->url = isset($this->current) ? $this->current->get_url() : '/' . _get::__namespace($this, 0) . ($this->view != '_default' ? '/' . $this->view : '');
        $push_state->title = $this->page_object->title;
        $push_state->data = (object) array(
            'url' => $push_state->url,
            'module' => get_class($this),
            'act' => isset($_REQUEST['ajax_act']) ? $_REQUEST['ajax_act'] : 'ajax_load',
            'request' => $_REQUEST,
            'id' => '#' . $this->view_object->get_page_selector(),
        );;
        $push_state->push = !isset($_REQUEST['is_popped']) ? true : !$_REQUEST['is_popped'];
        return $push_state;
    }
}
