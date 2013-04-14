<?php
class cms extends core_module {

    public static $url_base = '/cms/';
    /** @var table */
    public $current;
    public $module = 0;
    public $module_id = 0;

    public static function do_clear_filter() {
        unset($_SESSION['cms'][$_REQUEST['class']]);
        ajax::add_script('window.location = window.location');
        // TODO make this a true ajax act.
    }

    public function do_paginate() {
        ajax::add_script('window.location = window.location + "/page/" +' . $_REQUEST['value']);
    }

    public function __controller(array $path) {
        core::$page_config['title_tag'] = 'Admin Login - UKNXCL';
        core::$css = array('/inc/module/cms/css/cms.css');
        core::$js = array('/js/jquery/jquery.js', '/js/_ajax.js', ' /inc/module/cms/js/cms.js', '/js/jquery/colorbox.js');
        $this->view = 'login';
        if (isset($path[1]) && !empty($path[1]) && admin) {
            core::$page_config['pre_content'] = $this->get_nav();
            $this->view = $path[1];
            if (isset($path[2])) {
                $this->set_from_mid($path[2]);
                $this->npp = isset($_SESSION['cms'][$this->mid]) ? $_SESSION['cms'][$this->mid] : 25;
                $this->page = isset($path[4]) ? $path[4] : 1;

                $this->current_class = new $this->module->table_name;

                $this->where = array();
                foreach ($this->current_class->get_fields() as $field) {
                    if (isset($_SESSION['cms'][$this->module->table_name][$field->field_name]) && $_SESSION['cms'][$this->module->table_name][$field->field_name]) {
                        $this->where[$field->field_name] = $_SESSION['cms'][$this->module->table_name][$field->field_name];
                    }
                }
                $this->tot = db::result(db::get_query($this->module->table_name,array('count(*) AS count'), array('where_equals'=>$this->where),$parameters), $parameters)->count;
            }
        }
        if (isset($path[3]) && !empty($path[3]) && admin) {
            $this->current->do_retrieve_from_id(array(), $path[3]);
        }
        core::$page_config['body_class'] = 'module_cms cms_' . $this->view;

        parent::__controller($path);
    }

    function get_nav() {
        $html = html_node::create('ul#nav');
        $res = db::query('SELECT * FROM _cms_group WHERE live = 1 AND deleted = 0');
        while ($row = db::fetch($res)) {
            $sub = html_node::create('ul');
            $sres = db::query('SELECT * FROM _cms_modules WHERE live = 1 AND deleted = 0 AND gid = ' . $row->gid);
            while ($srow = db::fetch($sres)) {
                $sub->add_child(
                    html_node::create('li')->add_child(
                        html_node::create('span', html_node::inline('a', $srow->title, array('href' => '/cms/module/' . $srow->mid)))
                    )
                );
            }
            $html->add_child(html_node::create('li')->nest([
                        html_node::create('span', $row->title),
                        $sub
                    ]
                )
            );
        }
        $html->nest(html_node::create('a#new_module', 'New Module', array('href' => '/cms/new_module/')));
        return $html->get();
    }

    public function set_from_mid($mid) {
        $this->mid = $mid;
        $this->module = db::result('SELECT * FROM _cms_modules WHERE mid =:mid', array('mid' => $this->mid));
        $class = $this->module->table_name;
        $this->current = new $class();
        $this->current->mid = $this->mid;
    }

    public function do_reorder_fields() {
        if (isset($_REQUEST['mid']) && isset($_REQUEST['fid'])) {
            $this->set_from_mid($_REQUEST['mid']);
            $res = db::query('SELECT * FROM _cms_fields WHERE mid=:mid ORDER BY `position`', array('mid' => $_REQUEST['mid']));
            $fields = db::fetch_all($res);
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
                    db::query('UPDATE _cms_fields SET `position`=:position WHERE fid=:fid', array('position' => $field->new_pos, 'fid' => $field->fid));
                }
            }
            ajax::update($this->current->get_cms_edit_module()->get());
        }
    }

    public function get() {
    }

    public function get_admin_add() {
        return html_node::create('a#admin_edit.button', 'Add New ' . $this->module->title, ['href' => self::$url_base . 'edit/' . $this->mid]);
    }

    public function get_admin_edit() {
        return html_node::create('a#admin_edit.button', 'Edit Module', ['href' => self::$url_base . 'admin_edit/' . $this->mid]);
    }

    public function get_admin_new_module_form() {
        $form = new new_module_form();
        return $form->get_html();
    }

    public function get_inner() {
        $html = html_node::create('div#inner');
        $class = $this->module->table_name;
        $sres = $class::get_all(array($class . '.*'), array('limit' => ($this->page - 1) * $this->npp . ',' . $this->npp, 'where_equals' => $this->where));
        $html->nest($this->get_list($class, $sres));
        return $html;
    }

    public function get_list($obj, $elements) {
        $this->object = new $obj();
        $html = html_node::create('table')->nest(
            [
                $this->get_table_head($this->object),
                $this->get_table_rows($elements, $obj),
            ]
        );
        $nodes = array(
            $this->get_filters($this->object),
            $html,
            $this->get_pagi($elements->count())
        );
        return $nodes;
    }

    public function get_table_head(table $obj) {
        $node = html_node::create('thead');
        $node->add_child(html_node::create('th.edit', ''));
        foreach ($obj->get_fields() as $field) {
            if ($field->list) {
                $node->add_child(html_node::create('th.' . get_class($field) . '.' . $field->field_name . ($field->field_name == $obj->table_key ? '.primary' : ''), $field->title));
            }
        }
        $node->add_child(html_node::create('th.delete', ''));
        return $node;
    }

    public function get_table_rows($objects, $class) {
        $nodes = html_node::create('tbody');
        $objects->iterate(function ($obj) use ($nodes, $class) {
                $node = html_node::create('tr');
                $node->add_child(html_node::create('td.edit', html_node::inline('a.edit', '', array('href' => '/cms/edit/' . $this->mid . '/' . $obj->{$obj->table_key}))));
                $node->nest($obj->get_cms_list());
                $node->add_child(html_node::create('td.delete', html_node::inline('a.delete', '', array('href' => '/cms/delete/' . $this->mid . '/' . $obj->{$obj->table_key}))));
                $nodes->add_child($node);
            }
        );
        return $nodes;
    }

    public function get_filters() {
        $wrapper = html_node::create('div#filter_wrapper ul');
        $filter_form = new cms_filter_form(get_class($this->current));
        $filter_form->npp = $this->npp;
        $wrapper->nest($this->get_pagi('top'), $filter_form->get_html());
        return $wrapper;
    }

    public function get_pagi() {
        $node = html_node::create('span');
        if ($this->tot > $this->npp) {
            $pages = ceil($this->tot / $this->npp);
            if ($pages > 40) {
                $node = html_node::create('select#pagi.cf', '', array('data-ajax-change' => 'cms:do_paginate'));
                for ($i = 1; $i <= $pages; $i++) {
                    $attributes = array('value' => $i);
                    if ($this->page = $i) {
                        $attributes['selected'] = 'selected';
                    }
                    $node->add_child(html_node::create('option', $i, array('value' => $i)));
                }
            } else {
                $node = html_node::create('ul#pagi.cf');
                for ($i = 1; $i <= $pages; $i++) {
                    $node->add_child(html_node::create('li')->add_child(html_node::create('a', $i, array('href' => '/cms/module/' . $this->mid . '/page/' . $i))));
                }
            }
        }
        return $node;
    }

    public function get_new_field_form() {
        $form = new add_field_form();
        $form->mid = $this->mid;
        return $form->get_html();
    }

}
