<?php

abstract class core_module {
    public static $page_fields_to_retrieve = array('pid', 'body', 'title');
    /** @var table */
    public $current;
    public $view = '_default';
    public $page = 1;
    public $npp = 50;
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
        $this->set_page();
        core::$page_config->add_body_class('module_' . get_class($this), $this->view);
    }

    function set_page() {
        $this->page_object = new page();
        if (!isset($this->pid)) {
            $this->page_object->do_retrieve(self::$page_fields_to_retrieve, array('where_equals' => array('module_name' => get_class($this))));
        } else {
            $this->page_object->do_retrieve_from_id(self::$page_fields_to_retrieve, $this->pid);
        }
    }

    function get_main_nav() {
        $html = '';
        $pages = page::get_all(array(), array('where' => 'nav=1'));
        //$pages->iterate(function(page $page) use (&$html) {
        /** @var page $page */
        foreach ($pages as $page) {
            $html .= '<li ' . ($page->pid == core::$singleton->pid ? 'class="sel"' : '') . '>';
            $html .= '<a href="' . $page->get_url() . '" data-page-post=\'{"module":"' . (!empty($page->module_name) ? $page->module_name : 'pages') . '","act":"ajax_load"' . (empty($page->module_name) ? ',"page":' . $page->pid : '') . '}\'>' . $page->nav_title . '</a></li>';
        }
        //});
        return $html;
    }

    public function get_body() {
        $html = '';
        $pages = page::get_all(array(), array('where' => 'nav=1'));
        //$pages->iterate(function(page $page) use (&$html) {
        /** @var page $page */
        foreach ($pages as $page) {
            if ($page->pid == core::$singleton->pid) {
                $_REQUEST['page'] = $page->pid;
                $html .= '<div id="' . (!empty($page->module_name) ? $page->module_name : 'pages-' . $page->pid) . '">' . $this->get() . '</div>';
            } else {
                $html .= '<div id="' . (!empty($page->module_name) ? $page->module_name : 'pages-' . $page->pid) . '" class="loading" style="display:none"></div>';
            }
        }
        //});
        return $html;
    }

    public function set_view() {
        $file = root . '/inc/module/' . get_class($this) . '/view/' . $this->view . '.php';
        if (is_readable($file)) {
            include_once($file);
            $class = $this->view . '_view';
            $this->view_object = new $class;
            $this->view_object->module = $this;
        } else {
            if (debug) {
                throw new Exception('View not found, ' . $file);
            } else {

            }
        }
    }

    public function ajax_load() {
        $this->set_view();
        $this->view_object->get_view_ajax();
        $push_state = $this->get_push_state();
        ajax::push_state($push_state);
    }

    public function get_push_state() {
        $push_state = new push_state();
        $push_state->url = isset($this->current) ? $this->current->get_url() : '/' . get_class($this) . ($this->view != '_default' ? '/' . $this->view : '');
        $push_state->title = $this->page_object->title;
        $push_state->data = (object) array(
            'url' => $push_state->url,
            'module' => get_class($this),
            'act' => isset($_REQUEST['ajax_act']) ? $_REQUEST['ajax_act'] : 'ajax_load',
            'request' => $_REQUEST,
            'id' => '#' . $this->view_object->get_page_selector()
        );
        return $push_state;
    }
}
