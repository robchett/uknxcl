<?php

use classes\ajax;
use classes\ini;
use classes\module;
use classes\page_config;
use classes\push_state;
use classes\session;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

class core {

    public static array $push_state_ajax_calls = [];

    /** @var core */
    public static core $singleton;
    public static array $inline_script = [];
    public static array $global_script = [];
    public static array $js = ['/js/script.js'];
    public static array $css = ['/css/styles.css'];
    /** @var page_config */
    public static page_config $page_config;
    /** @var int */
    public int $pagination_page;
    public int $pid = 0;
    public array $path = [];
    public string $module_name = '';
    /** @var module */
    public module $module;

    /**
     *
     */
    public function __construct() {
        self::$js[] = 'http://maps.google.com/maps/api/js?libraries=geometry';
        self::$js[] = 'https://www.google.com/jsapi';
        self::$page_config = new page_config();
        self::$singleton = $this;
        $this->set_path(isset($_REQUEST['url']) ?: uri);
        define('cms', $this->path && $this->path[0] == 'cms');
        if (isset($_REQUEST['module'])) {
            $this->do_ajax();
        }
        $this->load_page();
    }

    /**
     * @param $uri
     */
    public function set_path($uri) {
        $uri_no_qs = trim(parse_url($uri, PHP_URL_PATH), '/');
        if ($uri_no_qs) {
            $this->path = explode('/', $uri_no_qs);
        }
        $this->pagination_page = $this->get_page_from_path();
        define('clean_uri', implode('/', $this->path));
    }

    public function get_page_from_path() {
        $count = count($this->path);
        if ($count >= 2 && $this->path[$count - 2] == 'page' && is_numeric(end($this->path))) {
            $page = end($this->path);
            unset($this->path[$count - 1]);
            unset($this->path[$count - 2]);
            return $page;
        }
        return 1;
    }

    #[NoReturn]
    public function do_ajax() {
        $class = $_REQUEST['module'];
        $function = $_REQUEST['act'];
        if ($class == 'core' || $class == 'this') {
            $module = 'core';
        } else {
            if (class_exists($class)) {
                $module = $class;
            } else {
                $module = '\\module\\' . $class . '\\controller';
            }
        }
        if (class_exists($module)) {
            $class = new ReflectionClass($module);
            if ($class->hasMethod($function)) {
                $method = new ReflectionMethod($module, $function);
                if ($method->isStatic()) {
                    $module::$function();
                } else if ($module != 'core') {
                    $object = new $module;
                    $object->$function();
                } else {
                    $this->$function();
                }
            }
        }
        ajax::do_serve();
        exit();
    }

    /**
     *
     */
    public function load_page() {
        if ($this->path) {
            if (!is_numeric($this->path[0])) {
                $this->module_name = $this->path[0];
            } else {
                $this->module_name = 'pages';
            }
        } else {
            $this->module_name = ini::get('site', 'default_module', 'pages');
        }

        if (!class_exists('module\\' . $this->module_name . '\controller')) {
            throw new Exception("Module $this->module_name is not defined");
        }

        $class_name = 'module\\' . $this->module_name . '\controller';
        $this->module = new $class_name();
        $this->module->__controller($this->path);
        $this->module->page = $this->pagination_page;
        if (!ajax) {
            $content = $this->module->view_object->get_page();
            $ajax = false;
        } else {
            $content = $this->module->view_object->get();
            $ajax = ajax::current();
        }
        $push_state = $this->module->get_push_state();
        if ($push_state) {
            $push_state->data->actions = array_merge($push_state->data->actions, self::$push_state_ajax_calls);
        }

        if (!ajax) {
            if ($push_state) {
                $push_state->type = push_state::REPLACE;
                $push_state->get();
            }
            echo $content;
        } else {
            ajax::set_current($ajax);
            if ($push_state) {
                ajax::push_state($push_state);
            }
            $class = new ReflectionClass('\classes\ajax');
            $function = $class->getMethod('inject');
            $function->invokeArgs(null, $content);
        }
    }

    /**
     * @param int $ignore_count The number of steps to ignore
     * @return string
     */
    public static function get_backtrace(int $ignore_count = 0): string {
        $trace = debug_backtrace();
        // Remove the get_backtrace entry
        array_shift($trace);
        for ($i = 0; $i < $ignore_count; $i++) {
            array_shift($trace);
        }
        $html = '<table><thead><tr><th>File</th><th>Line</th><th>Function</th></tr></thead>';
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

    public static function is_admin(): bool {
        return session::is_set('admin');
    }

    /**
     * @return string
     */
    #[Pure]
    public function get_js(): string {
        $script = '';
        $inner = '';
        foreach (self::$js as $js) {
            $script .= '<script src="' . $js . '"></script>';
        }
        foreach (self::$inline_script as $js) {
            $inner .= $js;
        }
        if (!empty($inner))
            $script .= '<script>' . implode(';', self::$global_script) . ';$(document).ready(function(){' . $inner . '});</script>';
        return $script;
    }

    /**
     * @return string
     */
    public function get_css(): string {
        $html = '';
        foreach (self::$css as $css) {
            $html .= '<link type="text/css" href="' . $css . '" rel="stylesheet"/>';
        }
        return $html;
    }
}
