<?php

namespace form;

use classes\attribute_callable;
use classes\db;
use classes\get;
use classes\icon;
use classes\table;
use core;
use html\node;
use JetBrains\PhpStorm\Pure;
use module\cms\controller;

class field extends node {

    public $fid;
    public $filter;
    public string $label;
    public $field_name;
    public $list;
    public $live;
    public $pre_text;
    public $post_text;
    public bool $raw = false;
    public bool $required = true;
    public array $attributes = ['type' => 'text'];
    public $hidden;
    public bool $disabled = false;
    public array $class = ['form-control'];
    public string $title;
    public array $wrapper_class = [];
    /** @var  form|table */
    public form|table $parent_form;
    public int|string|array $value = '';

    public function __construct($name, $options = []) {
        if (!empty($options)) {
            foreach ($options as $key => $val) {
                $this->$key = $val;
            }
        }
        $this->field_name = $name;
        if (!isset($this->label)) {
            $this->label = ucwords(str_replace('', ' ', $name));
        }
    }

    public static function sanitise_from_db($value) {
        return $value;
    }

    public function get_html_wrapper(): ?string {
        $html = '';
        $html .= $this->pre_text;

        if (!$this->hidden && isset($this->label) && !empty($this->label)) {
            $html .= node::create('label.control-label.col-md-' . $this->parent_form->bootstrap[0], ['for' => $this->field_name, 'id' => $this->field_name . '_wrapper'], $this->label);
        }
        $html .= node::create('div.control.col-md-' . $this->parent_form->bootstrap[1], [], $this->get_html());
        $html .= $this->post_text;
        return $html;
    }

    public function get_html(): string {
        $attributes = $this->attributes;
        $this->set_standard_attributes($attributes);
        return '<input ' . static::get_attributes($attributes) . ' value="' . htmlentities($this->get_value()) . '"/>';
    }

    public function set_standard_attributes(&$attributes) {
        if ($this->hidden) {
            $attributes['type'] = 'hidden';
        }
        if (!isset($attributes['name'])) {
            $attributes['name'] = $this->field_name;
        }
        if (!isset($attributes['id'])) {
            $attributes['id'] = $this->field_name;
        }
        if ($this->disabled) {
            $attributes['disabled'] = 'disabled';
        }
        if ($this->required) {
            $this->class[] = 'required';
        }
        $attributes['class'] = $this->class;
    }

    public function get_value() {
        return $this->parent_form->{$this->field_name};
    }

    public function set_value($val) {
        $this->parent_form->{$this->field_name} = $val;
    }

    public function do_validate(&$error_array) {
        if ($this->required && empty($this->parent_form->{$this->field_name})) {
            $error_array[$this->field_name] = $this->field_name . ' is required field';
        }
    }

    public function set_attrs($attrs): static {
        foreach ($attrs as $attr => $val) {
            $this->$attr = $val;
        }
        return $this;
    }

    public function set_attr($attr, $val): static {
        $this->$attr = $val;
        return $this;
    }

    public function add_class(string $classes): static {
        $this->class[] = $classes;
        return $this;
    }

    public function add_wrapper_class($val): static {
        $this->wrapper_class[] = $val;
        return $this;
    }

    #[Pure]
    public function get_class(): bool|string {
        if (!empty($this->class)) {
            return 'class="' . implode(' ', $this->class) . '"';
        }
        return false;
    }

    public function get_wrapper_class(): string {
        $classes = array_merge($this->wrapper_class, $this->parent_form->field_wrapper_class);
        $classes[] = get::__class_name($this) . '_wrapper';
        return '.' . implode('.', $classes);
    }

    public function set_from_request() {
        $this->parent_form->{$this->field_name} = (isset($_REQUEST[$this->field_name]) ? ($this->raw ? $_REQUEST[$this->field_name] : strip_tags($_REQUEST[$this->field_name])) : '');
    }

    public function get_cms_list_wrapper($value, $object_class, $id) {
        return $value;
    }

    public function set_from_row($row) {
        foreach ($row as $title => $val) {
            $this->{$title} = ($val);
        }
    }

    public function get_database_create_query(): ?string {
        return 'varchar(32)';
    }

    public function get_database_create_extra(): bool {
        return false;
    }

    public function get_cms_admin_edit(): array {
        $cols = [];
        $cols[] = node::create('td span.btn.btn-default.live' . ($this->live ? '' : '.not_live'), [], icon::get($this->live ? 'ok' : 'remove'));
        $cols[] = node::create('td span.btn.btn-default.edit', ['href' => '/' . $this->fid . '/?module=\module\cms\model\_cms_module&act=get_edit_field_form&fid=' . $this->fid, 'data-target' => '#modal', 'data-toggle' => 'modal'], icon::get('pencil'));
        $cols[] = "<td>{$this->fid}<td>";
        $cols[] = node::create('td', [], node::create('a.up.reorder.btn.btn-default', ['data-ajax-click' => attribute_callable::create([controller::class, 'do_reorder_fields']), 'data-ajax-post' => '{"mid":' . $this->parent_form->get_module_id() . ',"fid":' . $this->fid . ',"dir":"up"}'], icon::get('arrow-up')) . node::create('a.down.reorder.btn.btn-default', ['data-ajax-click' => 'cms:do_reorder_fields', 'data-ajax-post' => '{"mid":' . $this->parent_form->get_module_id() . ',"fid":' . $this->fid . ',"dir":"down"}'], icon::get('arrow-down')));
        $cols[] = "<td>{$this->title}<td>";
        $cols[] = "<td>{$this->field_name}<td>";
        $cols[] = node::create('td', [], get::__class_name($this));

        $fields = ['list', 'required', 'filter'];
        foreach ($fields as $field) {
            $list_options = ['data-ajax-change' => 'form\field_boolean:update_cms_setting', 'data-ajax-post' => '{"fid":' . $this->fid . ', "field":"' . $field . '"}', 'value' => 1, 'type' => 'checkbox'];
            if ($this->$field) {
                $list_options['checked'] = 'checked';
            }
            $cols[] = node::create('td input#' . $this->fid . '_list', $list_options);
        }
        $cols[] = node::create('td', [], ($this instanceof field_image) ? $this->get_image_edit_link() : '');
        return $cols;
    }

    public function update_cms_setting(): int {
        if (core::is_admin()) {
            db::update('_cms_field')->add_value($_REQUEST['field'], $_REQUEST['value'])->filter_field('fid', $_REQUEST['fid'])->execute();
        }
        return 1;
    }

    public function get_save_sql() {
        return $this->mysql_value($this->parent_form->{$this->field_name});
    }

    public function mysql_value($value) {
        return $value;
    }

    public function has_value(): bool {
        return isset($this->parent_form->{$this->field_name});
    }
}
