<?php

class field_select extends field {

    public $default = 'Please Choose';
    public $options = array();

    public function  __construct($title, $options = array()) {
        parent::__construct($title, $options);
        $this->class[] = 'picker';
    }

    public function get_html() {
        $html = '';
        $html .= '<select id="' . $this->field_name . '" name="' . $this->field_name . '" ' . $this->get_class() . ' ' . ($this->required ? ' false' : '') . '">' . "\n";
        if (!empty($this->default)) $html .= '<option value="default">' . $this->default . '</option>' . "\n";
        foreach ($this->options as $k => $v) {
            $html .= '<option value="' . $k . '" ' . ($this->parent_form->{$this->field_name} == $k ? 'selected="selected"' : '') . '>' . $v . '</option>' . "\n";

        }
        $html .= '</select>' . "\n";
        return $html;
    }

    public function do_validate(&$error_array) {
        if ($this->required && (empty($this->parent_form->{$this->field_name})))
            $error_array[$this->field_name] = $this->field_name . ' is required field';
        if ($this->parent_form->{$this->field_name} == 'default' && $this->required)
            $error_array[$this->field_name] = $this->field_name . ' please choose an option';
    }
}
