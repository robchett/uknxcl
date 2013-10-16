<?php

namespace core;

use classes\ajax;
use classes\get;
use classes\page_config;
use classes\push_state;
use module\cms\object\_cms_field;
use module\cms\object\_cms_module;
use module\pages\object\page;
use template\html;

abstract class core {

    /** @var core */
    public static $singleton;
    public static $inline_script = [];
    public static $js = ['/js/'];
    public static $css = ['/css/'];
    /** @var page_config */
    public static $page_config;
    public $body = '';
    public $pid = 0;
    public $pre_content = '';
    public $path = [];
    public $post_content = '';
    public $module_name = '';
    /** @var \classes\module */
    public $module;
    /** @var page */
    public $page;

    /**
     *
     */
    public function __construct() {
        self::$page_config = new page_config();
        self::$singleton = $this;
        $this->set_path(uri);
        define('cms', $this->path[0] == 'cms');

        if (isset($_REQUEST['module'])) {
            $this->do_ajax();
        }

        self::$page_config->title_tag = get::ini('title_tag', 'site_settings', 'NO Title tag!!!');
        $this->load_page();
    }

    public function do_ajax() {
        if ($_REQUEST['module'] == 'core' || $_REQUEST['module'] == 'this') {
            $module = $this;
        } else {
            if (class_exists($_REQUEST['module'])) {
                $module = new $_REQUEST['module']();
            } else {
                $class_name = '\\module\\' . $_REQUEST['module'] . '\\controller';
                $module = new $class_name();
            }
        }
        $module->{$_REQUEST['act']}();
        ajax::do_serve();
        exit();
    }

    /**
     *
     */
    public function load_page() {
        if (!is_numeric($this->path[0])) {
            $this->module_name = $this->path[0];
        } else {
            $this->module_name = 'pages';
        }

        if (class_exists('module\\' . $this->module_name . '\controller')) {
            $class_name = 'module\\' . $this->module_name . '\controller';
            $this->module = new $class_name();
            $this->module->__controller($this->path);
            $this->body = $this->module->view_object->get();
            $push_state = $this->module->get_push_state();
            if ($push_state) {
                if (!ajax) {
                    $push_state->type = push_state::REPLACE;
                    $push_state->get();
                } else {
                    ajax::push_state($push_state);
                }
            }
            if (!ajax) {
                $template = new html($this->module);
                echo $template->get();
            }
        }
    }

    /**
     *
     */
    public function set_page_from_path() {
        $this->page = new page();
        if (is_numeric($this->path[0])) {
            $this->page->do_retrieve_from_id(array(), (int) $this->path[0]);
        } else {
            $this->page->do_retrieve(array(), array('where_equals' => array('module_name' => $this->path[0])));
        }
        $this->pid = (isset($this->page->pid) ? $this->page->pid : 0);

    }

    /**
     * @param $uri
     */
    public function set_path($uri) {
        $uri_no_qs = parse_url($uri, PHP_URL_PATH);
        $this->path = explode('/', trim($uri_no_qs, '/'));
        if (!$this->path[0]) {
            $this->path[0] = get::ini('default_module', 'site_settings', 'pages');
        }
    }

    /**
     * @return string
     */
    public static function get_backtrace() {
        $trace = debug_backtrace();
        array_reverse($trace);
        unset($trace[count($trace) - 1]);
        $html = '<table><thead><th>File</th><th>Line</th><th>Function</th></thead>';
        foreach ($trace as $step) {
            $html .= '<tr>
            ' . (isset($step['file']) ? '<td>' . $step['file'] . '</td>' : '') . '
            ' . (isset($step['line']) ? '<td>' . $step['line'] . '</td>' : '') . '
            <td>' . (isset($step['class']) ? $step['class'] . (isset($step['type']) ? $step['type'] : '::') : '') . $step['function'] . '()</td>
            </tr>';
        }
        $html .= '</table>';
        return $html;
    }

    /**
     * @param $mid
     * @return string
     */
    public static function get_class_from_mid($mid) {
        $module = new _cms_module(['namespace', 'table_name'], $mid);
        return $module->get_class_name();
    }

    /**
     * @param $fid
     * @return _cms_field
     */
    public static function get_field_from_fid($fid) {
        return new _cms_field([], $fid);
    }

    /**
     * @return string
     */
    public function get_js() {
        $script = '';
        $inner = '';
        foreach (self::$js as $js) {
            $script .= '<script src="' . $js . '"></script>';
        }
        foreach (self::$inline_script as $js) {
            $inner .= $js;
        }
        if (!empty($inner))
            $script .= '<script>$(document).ready(function(){' . $inner . '});</script>';
        return $script;
    }

    /**
     * @return string
     */
    public function get_css() {
        $html = '';
        foreach (self::$css as $css) {
            $html .= '<link type="text/css" href="' . $css . '" rel="stylesheet"/>';
        }
        return $html;
    }
}
