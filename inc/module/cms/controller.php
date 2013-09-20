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
     * @var int
     */
    public $module = 0;
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

                $class = $this->module->namespace . '\\' . $this->module->table_name;
                $this->current_class = new $class;

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
        require_once (root . '/inc/module/cms/view/cms_view.php');
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
            $res = \db::query('SELECT * FROM _cms_fields WHERE mid=:mid ORDER BY `position`', array('mid' => $_REQUEST['mid']));
            $fields = \db::fetch_all($res);
            $reverse = false;
            if (isset($_REQUEST['dir']) && $_REQUEST['dir'] == 'down') {
                $reverse = true;
                $fields = array_reverse($fields);
            }
            $cnt = $reverse ? count($fields) + 1 : 0;
            $previous = null;
            foreach ($fields as $field) {
                $cnt += $reverse ? -1 : 1;
                $field->new_pos = $cnt;
                if ($field->fid == $_REQUEST['fid']) {
                    $field->new_pos = $previous->new_pos;
                    $previous->new_pos = $cnt;
                }
                $previous = $field;
            }
            if ($reverse) {
                $fields = array_reverse($fields);
            }

            foreach ($fields as $field) {
                if ($field->new_pos != $field->position) {
                    \db::query('UPDATE _cms_fields SET `position`=:position WHERE fid=:fid', array('position' => $field->new_pos, 'fid' => $field->fid));
                }
            }
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
        $wrapper = node::create('div#filter_wrapper ul');
        $filter_form = new cms_filter_form(get_class($this->current));
        $filter_form->npp = $this->npp;
        $wrapper->nest($this->get_pagi('top'), $filter_form->get_html());
        return $wrapper;
    }

    /**
     * @return node
     */
    public function get_inner() {
        $html = node::create('div#inner');
        /** @var \table_array $class */
        $class = $this->module->namespace . '\\' . $this->module->table_name;
        $sres = $class::get_all([], array('limit' => ($this->page - 1) * $this->npp . ',' . $this->npp, 'where_equals' => $this->where));
        $html->nest($this->get_list($class, $sres));
        return $html;
    }

    /**
     * @param $obj
     * @param \table_array $elements
     * @return array
     */
    public function get_list($obj, $elements) {
        $this->object = new $obj();
        $html = node::create('table')->nest(
            array(
                $this->get_table_head($this->object),
                $this->get_table_rows($elements, $obj),
            )
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
        $html = node::create('ul#nav');
        $res = \db::query('SELECT * FROM _cms_group WHERE live = 1 AND deleted = 0');
        while ($row = \db::fetch($res)) {
            $sub = node::create('ul');
            $sres = \db::query('SELECT * FROM _cms_modules WHERE live = 1 AND deleted = 0 AND gid = ' . $row->gid);
            while ($srow = \db::fetch($sres)) {
                $sub->add_child(
                    node::create('li')->add_child(
                        node::create('span', node::inline('a', $srow->title, array('href' => '/cms/module/' . $srow->mid)))
                    )
                );
            }
            $html->add_child(node::create('li')->nest(array(
                        node::create('span', $row->title),
                        $sub
                    )
                )
            );
        }
        if (isset($this->mid)) {
            $html->nest(node::create('li.right', node::inline('a', 'Edit Module', array('href' => '/cms/admin_edit/' . $this->mid, 'title' => 'Edit ' . get_class($this->current)))));
            $html->nest(node::create('li.right', node::inline('a', 'Add new ' . get_class($this->current), array('href' => '/cms/edit/' . $this->mid, 'title' => 'Add new ' . get_class($this->current)))));
        } else if ($this->view === 'module_list') {
            $html->nest(node::create('li.right', node::inline('a', 'Add new module', array('href' => '/cms/new_module/', 'title' => 'Add new module'))));
            $html->nest(node::create('li.right', node::inline('a', 'Add new module group', array('href' => "/cms/edit/19", 'title' => 'Add new module group'))));
        }
        $html->nest(node::create('li.right', node::inline('a', 'All Modules', array('href' => '/cms/module_list/', 'title' => 'View all modules'))));
        return $html->get();
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
        $node = node::create('span');
        if ($this->tot > $this->npp) {
            $pages = ceil($this->tot / $this->npp);
            if ($pages > 40) {
                $node = node::create('select#pagi.cf', '', array('data-ajax-change' => 'cms:do_paginate'));
                for ($i = 1; $i <= $pages; $i++) {
                    $attributes = array('value' => $i);
                    if ($this->page = $i) {
                        $attributes['selected'] = 'selected';
                    }
                    $node->add_child(node::create('option', $i, array('value' => $i)));
                }
            } else {
                $node = node::create('ul#pagi.cf');
                for ($i = 1; $i <= $pages; $i++) {
                    $node->add_child(node::create('li')->add_child(node::create('a', $i, array('href' => '/cms/module/' . $this->mid . '/page/' . $i))));
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
        $node->add_child(node::create('th.edit', ''));
        foreach ($obj->get_fields() as $field) {
            if ($field->list) {
                $node->add_child(node::create('th.' . get_class($field) . '.' . $field->field_name . ($field->field_name == $obj->table_key ? '.primary' : ''), $field->title));
            }
        }
        $node->add_child(node::create('th.delete', ''));
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
            $node->add_child(node::create('td.edit', node::inline('a.edit', '', array('href' => '/cms/edit/' . $this->mid . '/' . $obj->{$obj->table_key}))));
            $node->nest($obj->get_cms_list());
            $node->add_child(node::create('td.delete', node::inline('a.delete', '', array('href' => '/cms/delete/' . $this->mid . '/' . $obj->{$obj->table_key}))));
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
        $this->module = \db::result('SELECT * FROM _cms_modules WHERE mid =:mid', array('mid' => $this->mid));
        $class = $this->module->namespace . '\\' . $this->module->table_name;

        $this->current = new $class();
        $this->current->mid = $this->mid;
    }

}
