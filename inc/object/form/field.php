<?php

abstract class field extends html_element {
    public $label;
    public $field_name;
    public $pre_text;
    public $post_text;
    public $raw = false;
    public $required = true;
    public $attributes = array(
        'type' => 'text',
    );
    public $hidden;
    public $disabled = false;
    public $class = array();
    public $wrapper_class = array();
    public $parent_form;
    public $value = '';

    public function __construct($name, $options = array()) {
        if (!empty($options)) {
            foreach ($options as $key => $val) {
                $this->$key = $val;
            }
        }
        $this->field_name = $name;
        $this->label = ucwords(str_replace('', ' ', $name));
    }

    public function get_html_wrapper() {
        $html = '';
        $html .= $this->pre_text;

        if (!$this->hidden && isset($this->label) && !empty($this->label)) $html .= '<label id="' . $this->field_name . '_wrapper"><span>' . $this->label . '</span>' . "\n";
        $html .= $this->get_html() . "\n";
        if (!$this->hidden && isset($this->label) && !empty($this->label)) $html .= '</label>' . "\n";
        ;
        $html .= $this->post_text;
        return $html;
    }

    public function get_html() {
        return '<input ' . $this->get_attributes() . ' value="' . $this->parent_form->{$this->field_name} . '"/>' . "\n";
    }

    public function set_standard_attributes() {
        if ($this->hidden)
            $this->attributes['type'] = 'hidden';
        if (!isset($this->attributes['name']))
            $this->attributes['name'] = $this->field_name;
        if (!isset($this->attributes['id']))
            $this->attributes['id'] = $this->field_name;
        if ($this->disabled)
            $this->attributes['disabled'] = 'disabled';
        if ($this->required)
            $this->class[] = 'required';
        $this->attributes['class'] = implode(' ', $this->class);
    }

    public function do_validate(&$error_array) {
        if ($this->required && empty($this->parent_form->{$this->field_name})) {
            $error_array[$this->field_name] = $this->field_name . ' is required field';
        }
    }

    /**@return field */
    public function set_attr($attr, $val) {
        $this->$attr = $val;
        return $this;
    }

    public function set_value($val) {
        $this->parent_form->{$this->field_name} = $val;
    }

    public function add_class($val) {
        $this->class[] = $val;
        return $this;
    }

    public function add_wrapper_class($val) {
        $this->wrapper_class[] = $val;
        return $this;
    }

    public function get_class() {
        if (!empty($this->class)) {
            return 'class="' . implode(' ', $this->class) . '"';
        }
    }

    public function get_wrapper_class() {
        $this->wrapper_class[] =get_class($this) . '_wrapper';
        if (!empty($this->wrapper_class)) {
            return '.' . implode('.', $this->wrapper_class);
        }
    }

    public function set_from_request() {
        $this->parent_form->{$this->field_name} = (isset($_REQUEST[$this->field_name]) ? ($this->raw ? $_REQUEST[$this->field_name] : strip_tags($_REQUEST[$this->field_name])) : '');

    }

    public function get_cms_list_wrapper($value, $class, $id) {
        return $value;
    }

    public function set_from_row($row) {
        foreach ($row as $title => $val) {
            $this->{$title} = ($val);
        }
    }

    public function get_database_create_query() {
        return 'varchar(32)';
    }

    public function get_cms_admin_edit() {
        $cols = array();
        $cols[] = html_node::create('td', $this->fid);
        $cols[] = html_node::create('td')->nest(
            array(
                html_node::create('a.up.reorder', 'Up', array('data-ajax-click' => 'cms:do_reorder_fields', 'data-ajax-post' => '{"mid":' . $this->parent_form->mid . ',"fid":' . $this->fid . ',"dir":"up"}')),
                html_node::create('a.down.reorder', 'Down', array('data-ajax-click' => 'cms:do_reorder_fields', 'data-ajax-post' => '{"mid":' . $this->parent_form->mid . ',"fid":' . $this->fid . ',"dir":"down"}'))
            )
        );
        $cols[] = html_node::create('td', $this->title);
        $cols[] = html_node::create('td', $this->field_name);
        $cols[] = html_node::create('td', get_class($this));
        $list_options = array(
            'data-ajax-change' => 'field_boolean:update_cms_setting',
            'data-ajax-post' => '{"fid":' . $this->fid . ', "field":"list"}',
            'value' => 1,
            'type' => 'checkbox');
        if ($this->list) {
            $list_options['checked'] = 'checked';
        }
        $cols[] = html_node::create('td')->nest(html_node::create('input#' . $this->fid . '_list', null, $list_options));
        $required_options = array(
            'data-ajax-change' => 'field_boolean:update_cms_setting',
            'data-ajax-post' => '{"fid":' . $this->fid . ', "field":"required"}',
            'value' => 1,
            'type' => 'checkbox');
        if ($this->required) {
            $required_options['checked'] = 'checked';
        }
        $cols[] = html_node::create('td')->nest(html_node::create('input#' . $this->fid . '_required', null, $required_options));
        $filter_options = array(
            'data-ajax-change' => 'field_boolean:update_cms_setting',
            'data-ajax-post' => '{"fid":' . $this->fid . ', "field":"filter"}',
            'value' => 1,
            'type' => 'checkbox');
        if ($this->filter) {
            $filter_options['checked'] = 'checked';
        }
        $cols[] = html_node::create('td')->nest(html_node::create('input#' . $this->fid . '_filter', null, $filter_options));
        return $cols;
    }

    public function update_cms_setting() {
        if (admin) {
            db::query('UPDATE _cms_fields SET ' . $_REQUEST['field'] . '=:value WHERE fid=:fid', array(
                    'value' => $_REQUEST['value'],
                    'fid' => $_REQUEST['fid'],
                )
            );
        }
        return 1;
    }

    public function get_save_sql(&$sql_array, &$parameters) {
        $sql_array[] = '`' . $this->field_name . '`=:' . $this->field_name;
        $parameters[$this->field_name] = $this->mysql_value($this->parent_form->{$this->field_name});
    }

    public function mysql_value($value) {
        return $value;
    }
}
