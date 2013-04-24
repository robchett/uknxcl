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
    );
    public $view = '';
    public $page = 1;
    public $npp = 50;

    public function __controller(array $path) {

        if(count($path) > 3 && $path[count($path)-2] == 'page') {
            if(end($path) == 'all') {
                $this->npp = 99999999;
                $this->page = 1;
            } else {
                $this->page = end($path);
            }
        }
        if (empty($this->view)) {
            $file = root . '/inc/module/_default/view/default.php';
        } else
            $file = root . '/inc/module/' . get_class($this) . '/view/' . $this->view . '.php';
        if (is_readable($file))
            include_once($file);
        else
            throw new Exception('View not found, ' . $file);
    }

    function get_main_nav() {
        $html = '';
        $pages = page::get_all(array(),array('where'=>'nav=1'));
        //$pages->iterate(function(page $page) use (&$html) {
        foreach($pages as $page) {
            $fn = (!empty($page->module_name) ? $page->module_name : 'pages-' .$page->pid );
            $html .= '<li id="nav-' . $fn . '" ' . ($page->pid == core::$singleton->pid ? 'class="s"' : ''). '>';
            $html .='<a href="' . $page->get_url() . '" data-page-post=\'{"module":"' . (!empty($page->module_name) ? $page->module_name : 'pages') . '","act":"ajax_load"' . (empty($page->module_name) ? ',"page":' . $page->pid : '' ) . '}\'>' . $page->nav_title  . '</a></li>';
        }
        //});
        return $html;
    }

    public function get_body() {
        $html = '';
        $pages = page::get_all(array(),array('where'=>'nav=1'));
        //$pages->iterate(function(page $page) use (&$html) {
        foreach($pages as $page) {
            if ($page->pid == core::$singleton->pid) {
                $_REQUEST['page'] = $page->pid;
                $html .= '<div id="' . (!empty($page->module_name) ? $page->module_name : 'pages-' . $page->pid) . '">' . $this->get() . '</div>';
                $html .= '<script>loaded_modules = {"' . $page->get_url() . '":true}</script>';
            } else
                $html .= '<div id="' . (!empty($page->module_name) ? $page->module_name : 'pages-' . $page->pid) . '" class="loading" style="display:none"></div>';
        }
        //});
        return $html;
    }

    public function ajax_load() {
        $push_state = new push_state();
        if(get_class($this) == 'pages') {
            $content = $this->get();
            ajax::inject('#pages-' . $this->current->pid, 'append', $content);
            $push_state->url = $this->current->get_url();
            $push_state->title = $this->current->nav_title;
            $push_state->data = (object) array(
            'page' => array(
            'url' => $this->current->get_url(),
        )
            );
        } else {
            ajax::inject('#' . get_class($this), 'append', $this->get());
            $push_state->url = '/' . get_class($this);
            $push_state->title = self::$default_modules[get_class($this)];
            $push_state->data = (object) array(
                'page' => array(
                    'url' => get_class($this),
                )
            );
        }
        ajax::push_state($push_state);
    }

    public abstract function get();

}
