<?php
namespace cms;

use html\node;

/**
 * Class controller
 * @package cms
 */
class controller extends \core_module {

    /**
     * @var string
     */
    public static $url_base = '/cms/';
    /** @var \table */
    public $current;
    /** @var \table */
    public $current_class;
    /**
     * @var
     */
    public $mid;
    /**
     * @var _cms_modules
     */
    public $module;
    /**
     * @var int
     */
    public $module_id = 0;
    /**
     * @var
     */
    public $object;
    /**
     * @var
     */
    public $tot;
    /**
     * @var
     */
    public $where;

    /**
     *
     */
    public static function do_clear_filter() {
        unset($_SESSION['cms'][$_REQUEST['_class_name']]);
        $cms_filter_form = new cms_filter_form();
        $cms_filter_form->do_submit();
        unset($_SESSION['cms'][$_REQUEST['_class_name']]);
    }

    /**
     * @param array $path
     */
    public function __controller(array $path) {
        \core::$page_config->title_tag = 'Admin Login - UKNXCL';
        \core::$css = array('/inc/module/cms/css/cms.css');
        \core::$js = array('/js/jquery/jquery.js', '/js/_ajax.js', ' /inc/module/cms/js/cms.js', '/js/jquery/colorbox.js', '/plugins/ckeditor/ckeditor.js');
        if (admin && !isset($path[1])) {
            $path[1] = 'dashboard';
        }
        $this->view = 'login';
        if (isset($path[1]) && !empty($path[1]) && admin) {
            $this->view = $path[1];
            if (isset($path[2])) {
                $this->set_from_mid($path[2]);
                $this->npp = isset($_SESSION['cms'][$this->module->table_name]['npp']) && !empty($_SESSION['cms'][$this->module->table_name]['npp']) ? $_SESSION['cms'][$this->module->table_name]['npp'] : 25;
                $this->page = isset($path[4]) ? $path[4] : 1;
                $this->current_class = $this->module->get_class();
                $this->where = array();
                foreach ($this->current_class->get_fields() as $field) {
                    if (isset($_SESSION['cms'][$this->module->table_name][$field->field_name]) && $_SESSION['cms'][$this->module->table_name][$field->field_name]) {
                        $this->where[$field->field_name] = $_SESSION['cms'][$this->module->table_name][$field->field_name];
                    }
                }
                $this->tot = \db::result(\db::get_query($this->module->table_name, array('count(*) AS count'), array('where_equals' => $this->where), $parameters), $parameters)->count;
            }
            \core::$page_config->pre_content = $this->get_main_nav();
        }
        if (isset($path[3]) && !empty($path[3]) && admin) {
            $this->current->do_retrieve_from_id(array(), $path[3]);
        }
        parent::__controller($path);
    }

    /**
     *
     */
    public function set_view() {
        require_once(root . '/inc/module/cms/view/cms_view.php');
        parent::set_view();
    }

    /**
     * @return bool
     */
    public function get_push_state() {
        return false;
    }

    /**
     *
     */
    public function do_paginate() {
        \ajax::add_script('window.location = window.location + "/page/" +' . $_REQUEST['value']);
    }

    /**
     *
     */
    public function do_reorder_fields() {
        if (isset($_REQUEST['mid']) && isset($_REQUEST['fid'])) {
            $this->set_from_mid($_REQUEST['mid']);
            $fields = _cms_fields::get_all([], ['where_equals' => ['mid' => $_REQUEST['mid']]]);
            $reverse = false;
            if (isset($_REQUEST['dir']) && $_REQUEST['dir'] == 'down') {
                $reverse = true;
                $fields->reverse();
            }
            $cnt = $reverse ? count($fields) + 1 : 0;
            /** @var _cms_fields $previous */
            $previous = $fields[0];
            $fields->iterate(function (_cms_fields $field) use (&$previous, $reverse, &$cnt) {
                    $cnt += $reverse ? -1 : 1;
                    $field->position = $cnt;
                    if ($field->fid == $_REQUEST['fid']) {
                        $field->position = $previous->position;
                        $previous->position = $cnt;
                    }
                    $previous = $field;
                }
            );
            if ($reverse) {
                $fields->reverse();
            }
            $fields->uasort(function (_cms_fields $a, _cms_fields $b) {
                    return $b->position - $a->position;
                }
            );
            $fields->iterate(function (_cms_fields $field) {
                    \db::update('_cms_fields')->add_value('position', $field->position)->filter_field('fid', $field->fid)->execute();
                }
            );
            \ajax::update($this->current->get_cms_edit_module()->get());
        }
    }

    /**
     *
     */
    public function get() {
    }

    /**
     * @return node
     */
    public function get_admin_new_module_form() {
        $form = new new_module_form();
        return $form->get_html();
    }

    /**
     * @return node
     */
    public function get_filters() {
        $filter_form = new cms_filter_form(get_class($this->current));
        $filter_form->npp = $this->npp;
        $wrapper = node::create('div#filter_wrapper ul', [], $this->get_pagi('top') . $filter_form->get_html());
        return $wrapper;
    }

    /**
     * @return node
     */
    public function get_inner() {
        /** @var \table_array $class */
        $class = $this->module->get_class_name();
        $sres = $class::get_all([], array('limit' => ($this->page - 1) * $this->npp . ',' . $this->npp, 'where_equals' => $this->where));

        $html = node::create('div#inner', [], $this->get_list($class, $sres));
        return $html;
    }

    /**
     * @param $obj
     * @param \table_array $elements
     * @return array
     */
    public function get_list($obj, $elements) {
        $this->object = new $obj();
        $html = node::create('table', [],
            $this->get_table_head($this->object) .
            $this->get_table_rows($elements, $obj)
        );
        $nodes = array(
            $this->get_filters($this->object),
            $html,
            $this->get_pagi($elements->count())
        );
        return $nodes;
    }

    /**
     * @return string
     */
    function get_main_nav() {
        $groups = _cms_group::get_all([]);
        $html = node::create('ul#nav', [],
            $groups->iterate_return(
                function (_cms_group $row) {
                    $modules = _cms_modules::get_all([], ['where_equals' => ['gid' => $row->gid]]);
                    return node::create('li', [],
                        node::create('span', [], $row->title) .
                        node::create('ul', [],
                            $modules->iterate_return(
                                function (_cms_modules $srow) {
                                    return node::create('li span a', ['href' => '/cms/module/' . $srow->mid], $srow->title);
                                }
                            )
                        )
                    );
                }
            )
        );
        if (isset($this->mid)) {
            $html->nest(node::create('li.right a', ['href' => '/cms/admin_edit/' . $this->mid, 'title' => 'Edit ' . get_class($this->current)], 'Edit Module'));
            $html->nest(node::create('li.right a', ['href' => '/cms/edit/' . $this->mid, 'title' => 'Add new ' . get_class($this->current)], 'Add new ' . get_class($this->current)));
        } else if ($this->view === 'module_list') {
            $html->nest(node::create('li.right a', ['href' => '/cms/new_module/', 'title' => 'Add new module'], 'Add new module'));
            $html->nest(node::create('li.right a', ['href' => "/cms/edit/19", 'title' => 'Add new module group'], 'Add new module group'));
        }
        $html->nest(node::create('li.right a', ['href' => '/cms/module_list/', 'title' => 'View all modules'], 'All Modules'));
        return $html;
    }

    /**
     * @return node
     */
    public function get_new_field_form() {
        $form = new add_field_form();
        $form->mid = $this->mid;
        return $form->get_html();
    }

    /**
     * @return node
     */
    public function get_pagi() {
        $node = node::create('div');
        if ($this->tot > $this->npp) {
            $pages = ceil($this->tot / $this->npp);
            if ($pages > 40) {
                $node = node::create('select#pagi.cf', ['data-ajax-change' => 'cms:do_paginate']);
                for ($i = 1; $i <= $pages; $i++) {
                    $attributes = ['value' => $i];
                    if ($this->page = $i) {
                        $attributes['selected'] = 'selected';
                    }
                    $node->add_child(node::create('option', ['value' => $i], $i));
                }
            } else {
                $node = node::create('ul#pagi.cf');
                for ($i = 1; $i <= $pages; $i++) {
                    $node->add_child(node::create('li a', ['href' => '/cms/module/' . $this->mid . '/page/' . $i], $i));
                }
            }
        }
        return $node;
    }

    /**
     * @param \table $obj
     * @return node
     */
    public function get_table_head($obj) {
        $node = node::create('thead');
        $node->add_child(node::create('th.edit'));
        foreach ($obj->get_fields() as $field) {
            if ($field->list) {
                $node->add_child(node::create('th.' . get_class($field) . '.' . $field->field_name . ($field->field_name == $obj->table_key ? '.primary' : ''), [], $field->title));
            }
        }
        $node->add_child(node::create('th.delete'));
        return $node;
    }

    /**
     * @param \table[] $objects
     * @return node
     */
    public function get_table_rows($objects) {
        $nodes = node::create('tbody');
        //$objects->iterate(function ($obj) use ($nodes, $class) {
        foreach ($objects as $obj) {
            $node = node::create('tr');
            $node->add_child(node::create('td.edit a.edit', ['href' => '/cms/edit/' . $this->mid . '/' . $obj->{$obj->table_key}]));
            $node->nest($obj->get_cms_list());
            $node->add_child(node::create('td.delete a.delete', ['href' => '/cms/delete/' . $this->mid . '/' . $obj->{$obj->table_key}]));
            $nodes->add_child($node);
        }
        //});
        return $nodes;
    }

    /**
     * @param $mid
     */
    public function set_from_mid($mid) {
        $this->mid = $mid;
        $this->module = new _cms_modules([], $this->mid);
        $this->current = $this->module->get_class();
        $this->current->mid = $this->mid;
    }

}
