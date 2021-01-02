<?php

namespace form;

use classes\attribute_callable;
use html\node;

class field_boolean extends field {

    public array $class = [];

    public function __construct($title = '', $options = []) {
        parent::__construct($title, $options);
        $this->value = false;
        $this->attributes['type'] = 'checkbox';
    }

    public function do_validate(&$error_array) {
//        if (!is_bool($this->parent_form->{$this->field_name})) {
//            $error_array[$this->field_name] = $this->field_name . ' is not a valid boolean';
//        }
    }

    public function set_from_request() {
        $this->parent_form->{$this->field_name} = isset($_REQUEST[$this->field_name]);
    }

    public function get_html_wrapper(): string {
        $html = '';
        $html .= $this->pre_text;
        $html .= node::create('div.col-md-offset-' . $this->parent_form->bootstrap[0] . '.col-md-' . $this->parent_form->bootstrap[1] . ' div.checkbox label', [], $this->get_html() . $this->label);
        $html .= $this->post_text;
        return $html;
    }

    public function get_html(): string {
        $attributes = $this->attributes;
        $this->set_standard_attributes($attributes);
        if ($this->required) {
            $this->class[] = 'required';
            $this->required = 0;
        }
        if ($this->parent_form->{$this->field_name}) {
            $attributes['checked'] = 'checked';
        }
        return '<input ' . static::get_attributes($attributes) . '/>';
    }

    public function get_cms_list_wrapper($value, $object_class, $id): string {
        $this->attributes['data-ajax-click'] = attribute_callable::create([$object_class, 'do_cms_update']);
        $this->attributes['data-ajax-post'] = '{"field":"' . $this->field_name . '", "value":' . (int)!$this->parent_form->{$this->field_name} . ',"id":' . $id . '}';
        $this->attributes['id'] = (isset($this->attributes['id']) ? $this->attributes['id'] : $this->field_name) . '_' . $id;
        $this->attributes['data-ajax-shroud'] = '#' . $this->field_name . '_' . $this->parent_form->{$this->parent_form->get_primary_key_name()};
        return $this->get_html();
    }

    public function mysql_value($value) {
        return (int) $value;
    }
}
