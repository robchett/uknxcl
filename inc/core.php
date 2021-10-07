<?php

use classes\ajax;
use classes\module;
use classes\page_config;
use classes\push_state;
use classes\session;
use JetBrains\PhpStorm\NoReturn;

class core {

    /** @var core */
    public static core $singleton;
    /** @var string[] */
    public static array $inline_script = [];
    /** @var string[] */
    public static array $global_script = [];
    /** @var string[] */
    public static array $js = ['/js/script.js'];
    /** @var string[] */
    public static array $css = ['/css/styles.css'];
    public int $pagination_page;
    public int $pid = 0;
    /** @var string[] */
    public array $path = [];
    public string $module_name = '';
    public module $module;


    public function __construct() {
        self::$js[] = 'https://maps.google.com/maps/api/js?libraries=geometry';
        self::$js[] = 'https://www.google.com/jsapi';
        self::$singleton = $this;
        $this->set_path((string) ($_REQUEST['url'] ?? uri));
        define('cms', $this->path && $this->path[0] == 'cms');
        if (isset($_REQUEST['module'])) {
            $this->do_ajax();
        }
        $this->load_page();
    }

    public function set_path(string $uri): void {
        $uri_no_qs = trim(parse_url($uri, PHP_URL_PATH), '/');
        if ($uri_no_qs) {
            $this->path = explode('/', $uri_no_qs);
        }
        $this->pagination_page = $this->get_page_from_path();
        define('clean_uri', implode('/', $this->path));
    }

    public function get_page_from_path(): int {
        $count = count($this->path);
        if ($count >= 2 && $this->path[$count - 2] == 'page' && is_numeric(end($this->path))) {
            $page = end($this->path);
            return (int) $page;
        }
        return 1;
    }

    public function do_ajax(): void {
        $class = (string) $_REQUEST['module'];
        $function = (string) $_REQUEST['act'];
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
                    /** @psalm-suppress MixedMethodCall */
                    $module::$function();
                } else if ($module != 'core') {
                    /** @psalm-suppress MixedMethodCall */
                    $object = new $module;
                    /** @psalm-suppress MixedMethodCall */
                    $object->$function();
                } else {
                    $this->$function();
                }
            }
        }
        ajax::do_serve();
        exit();
    }

    public function load_page(): void {
        $this->module_name = 'pages';
        if ($this->path && !is_numeric($this->path[0])) {
            $this->module_name = $this->path[0];
        }
        
        $module = match ($this->module_name) {
            'add_flight' => \module\add_flight\controller::class,
            'cms' => \module\cms\controller::class,
            'comps' => \module\comps\controller::class,
            'converter' => \module\converter\controller::class,
            'flight_info' => \module\flight_info\controller::class,
            'latest' => \module\latest\controller::class,
            'mass_overlay' => \module\mass_overlay\controller::class,
            'news' => \module\news\controller::class,
            'planner' => \module\planner\controller::class,
            'stats' => \module\stats\controller::class,
            'tables' => \module\tables\controller::class, 
            default => \module\pages\controller::class,
        };

        $this->module = new $module($this->path);
        $this->module->page = $this->pagination_page;
        $push_state = $this->module->view_object->get_push_state();

        if (!ajax) {
            if ($push_state) {
                $push_state->get();
            }  
            echo $this->module->view_object->get_page();
        } else {
            $this->module->view_object->get();
            if ($push_state) {
                ajax::push_state($push_state);
            }
        }
    }

    /**
     * @param int $ignore_count The number of steps to ignore
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
            <td>' . (isset($step['class']) ? $step['class'] . ($step['type'] ?? '::') : '') . $step['function'] . '()</td>
            </tr>';
        }
        $html .= '</table>';
        return $html;
    }

    public static function is_admin(): bool {
        return session::is_set('admin');
    }

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

    public function get_css(): string {
        $html = '';
        foreach (self::$css as $css) {
            $html .= '<link type="text/css" href="' . $css . '" rel="stylesheet"/>';
        }
        return $html;
    }
}
