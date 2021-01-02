<?php

namespace form;

class field_select extends field {

    public string $default = 'Please Choose';
    public array $options = [];
    public array $attributes = [];

    public function __construct($title, $options = []) {
        parent::__construct($title, $options);
        $this->class[] = 'picker';
    }

    public function get_html(): string {
        $attributes = $this->attributes;
        $this->set_standard_attributes($attributes);
        $html = '<select ' . static::get_attributes($attributes) . '>';
        if (!empty($this->default) && !$this->required) {
            $html .= '<option value="default">' . $this->default . '</option>';
        }
        foreach ($this->options as $k => $v) {
            $html .= '<option value="' . $k . '" ' . ($this->parent_form->{$this->field_name} == $k ? 'selected="selected"' : '') . '>' . $v . '</option>';

        }
        $html .= '</select>';
        return $html;
    }

    public function do_validate(&$error_array) {
        /*if ($this->required && (empty($this->parent_form->{$this->field_name})))
            $error_array[$this->field_name] = $this->field_name . ' is required field';*/
        if ($this->parent_form->{$this->field_name} == 'default' && $this->required) {
            $error_array[$this->field_name] = $this->field_name . ' please choose an option';
        }
    }
}
