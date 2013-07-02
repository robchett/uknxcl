<?php

abstract class core_module {
    public static $default_modules = array(
        'latest' => 'Latest',
        'news' => 'News',
        'rules' => 'Rules',
        'add_flight' => 'New Flight',
        'contact' => 'Contact',
        'comps' => 'Comps',
        'tables' => 'Tables',
        'planner' => 'Planner',
        'cms' => 'CMS'
    );
    /** @var table */
    public $current;
    public $view = '_default';
    public $page = 1;
    public $npp = 50;
    public $view_object;

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

        core::$page_config->add_body_class('module_' . get_class($this), $this->view);
    }

    function get_main_nav() {
        $html = '';
        $pages = page::get_all(array(), array('where' => 'nav=1'));
        //$pages->iterate(function(page $page) use (&$html) {
        /** @var page $page */
        foreach ($pages as $page) {
            $fn = (!empty($page->module_name) ? $page->module_name : 'pages-' . $page->pid);
            $html .= '<li id="nav-' . $fn . '" ' . ($page->pid == core::$singleton->pid ? 'class="s"' : '') . '>';
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

    public function get_page_selector() {
        return get_class($this);
    }

    public function set_view() {
        $file = root . '/inc/module/' . get_class($this) . '/view/' . $this->view . '.php';
        if (is_readable($file)) {
            include_once($file);
            $class = $this->view . '_view';
            $this->view_object = new $class;
            $this->view_object->module = $this;
        } else {
            if(debug) {
                throw new Exception('View not found, ' . $file);
            } else {

            }
        }
    }

    public function ajax_load() {
        $this->set_view();
        $content = $this->view_object->get_view();
        ajax::inject('#main', 'append', '<div id="' . $this->get_page_selector() . '">' . $content . '</div>');
        $push_state = $this->get_push_state();
        ajax::push_state($push_state);
    }

    public function get_push_state() {
        $push_state = new push_state();
        $push_state->url = '/' . get_class($this);
        $push_state->title = self::$default_modules[get_class($this)];
        $push_state->data = (object) array(
            'page' => array(
                'url' => '/' . get_class($this),
            ),
            'post' => array(
                'module' => get_class($this),
                'act' => 'ajax_load'
            )
        );
        return $push_state;
    }
}
