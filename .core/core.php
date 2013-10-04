<?php

namespace core;

abstract class core {

    /** @var core */
    public static $singleton;
    public static $inline_script = array();
    public static $js = array();
    public static $css = array('/css/');
    /** @var \classes\page_config */
    public static $page_config;
    public $body = '';
    public $pid = 0;
    public $pre_content = 'test';
    public $post_content = 'test';
    public $module_name = 'latest';
    /** @var \classes\module */
    public $module;
    /** @var \module\pages\object\page */
    public $page;

    /**
     *
     */
    public function __construct() {
        self::$page_config = new \classes\page_config();
        self::$singleton = $this;
        \classes\db::default_connection();
        $this->set_path(uri);
        self::$page_config->title_tag = 'UKNXCL National Cross Country League';

        if (!(isset($this->path[0])) || empty($this->path[0])) {
            $this->path[0] = 'latest';
        }
        define('cms', $this->path[0] == 'cms');

        if (isset($_REQUEST['module'])) {

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
            \classes\ajax::do_serve();
            exit();
        }
        $this->set_page_from_path();

        if (!$this->pid && is_numeric($this->path[0])) {
            \classes\get::header_redirect(host . '/');
            die();
        }
        \core::$js[] = 'http://maps.google.com/maps/api/js?libraries=geometry&amp;sensor=false';
        \core::$js[] = 'https://www.google.com/jsapi';
        \core::$js[] = '/js/';

        $this->load_page();
    }

    /**
     *
     */
    public function load_page() {
        if (isset($this->path[0])) {
            if (!is_numeric($this->path[0])) {
                $this->module_name = $this->path[0];
            } else {
                $this->module_name = 'pages';
            }
        } else {
            $this->module_name = 'latest';
        }

        if (class_exists('module\\' . $this->module_name . '\controller')) {
            $class_name = 'module\\' . $this->module_name . '\controller';
            $this->module = new $class_name();
            $this->module->__controller($this->path);
            $this->body = $this->module->view_object->get();
            $push_state = $this->module->get_push_state();
            if ($push_state) {
                if (!ajax) {
                    $push_state->type = \classes\push_state::REPLACE;
                    $push_state->get();
                } else {
                    \classes\ajax::push_state($push_state);
                }
            }
            if (!ajax) {
                require_once(root . '/index_screen.php');
            }
        }
    }

    /**
     *
     */
    public function set_page_from_path() {
        $this->page = new \module\pages\object\page();
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
        $module = new \module\cms\object\_cms_module(['namespace', 'table_name'], $mid);
        return $module->get_class_name();
    }

    /**
     * @param $fid
     * @return \module\cms\object\_cms_field
     */
    public static function get_field_from_fid($fid) {
        return new \module\cms\object\_cms_field([], $fid);
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
