<?php


class core {
    /** @var core */
    public static $singleton;
    public static $inline_script = array();
    public static $js = array();
    public static $css = array('/css');
    public static $page_config = array(
        'title_tag' => 'UKNXCL National Cross Country League',
        'pre_content' => '',
        'post_content' => '',
        'body_class' => '',
    );
    public $body = '';
    public $pid = 0;
    public $pre_content = 'test';
    public $post_content = 'test';
    public $module_name = 'latest';
    /** @var page */
    public $page;

    public function __construct() {
        self::$singleton = $this;
        db::connect();
        $this->path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

        if (!(isset($this->path[0])) || empty($this->path[0])) {
            $this->path[0] = 'latest';
        }
        define('cms',$this->path[0] == 'cms');

        if (isset($_REQUEST['module'])) {
            $module = new $_REQUEST['module']();
            $module->{$_REQUEST['act']}();
            ajax::do_serve();
            exit();
        }
        if (admin) {
            core_module::$default_modules['comp_maker'] = 'Comp Maker';
        }

        $this->set_page_from_path();

        if (!$this->pid && is_numeric($this->path[0])) {
            header('Location: http://uk.local.com/');
            die();
        }
        core::$js[] = 'http://maps.google.com/maps/api/js?libraries=geometry&amp;sensor=false';
        core::$js[] = 'https://www.google.com/jsapi';
        core::$js[] = '/js/jquery/jquery.js';
        core::$js[] = '/js/jquery/colorbox.js';
        core::$js[] = '/js';

        if (isset($this->path[0])) {
            if (!is_numeric($this->path[0])) {
                $this->module_name = $this->path[0];
            } else {
                $this->module_name = 'pages';
            }
        } else {
            $this->module_name = 'latest';
        }
        $this->module = new $this->module_name();

        ob_start();
        $this->module->__controller($this->path);
        $this->body = ob_get_contents();
        ob_end_clean();
        $push_state = new push_state();
        $push_state->type = push_state::REPLACE;
        $push_state->url = $_SERVER['REQUEST_URI'];
        $push_state->data = (object) array('page' => array('url' => $this->module_name));
        $push_state->get();
        require_once('/index_screen.php');
    }

    public function set_page_from_path() {
        $this->page = new page();
        if (is_numeric($this->path[0])) {
            $this->page->do_retrieve_from_id(array(), (int) $this->path[0]);
        } else {
            $this->page->do_retrieve(array(), array('where_equals' => array('module_name' => $this->path[0])));
        }
        $this->pid = (isset($this->page->pid) ? $this->page->pid : 0);

    }

    public static function get_backtrace() {
        $trace = debug_backtrace();
        array_reverse($trace);
        unset($trace[count($trace) - 1]);
        $html = '<table><thead><th>File</th><th>Line</th><th>Function</th></thead>';
        foreach ($trace as $step) {
            $html .= '<tr>
            ' . (isset($step['file']) ? '<td>' . $step['file'] . '</td>' : '' ) . '
            ' . (isset($step['line']) ? '<td>' . $step['line'] . '</td>' : '' ) . '
            <td>' . (isset($step['class']) ? $step['class'] . (isset($step['type']) ? $step['type'] : '::') : '') . $step['function'] . '()</td>
            </tr>';
        }
        $html .= '</table>';
        return $html;
    }

    public static function get_class_from_mid($mid) {
        $res = db::query('SELECT table_name FROM _cms_modules WHERE mid=:mid', array('mid' => $mid));
        $row = db::fetch($res);
        return $row->table_name;
    }

    public static function get_field_from_fid($fid) {
        $res = db::query('SELECT field_name FROM _cms_fields WHERE fid=:fid', array('fid' => $fid));
        $row = db::fetch($res);
        return $row->field_name;
    }

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

    public function get_css() {
        $html = '';

        foreach (self::$css as $css) {
            $html .= '<link type="text/css" href="' . $css . '" rel="stylesheet"/>';
        }
        return $html;
    }
}
