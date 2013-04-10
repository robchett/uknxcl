<?php

class field_datetime extends field {
    public function __construct($title, $options = array()) {
        parent::__construct($title, $options);
        $this->attributes['pattern'] = '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}';
    }

    public function set_value($val) {
        $this->parent_form->{$this->field_name} = date('d/m/Y h:i:s', strtotime($val));
    }

    public function mysql_value($value) {
        return date('Y-m-d h:i:s', strtotime(str_replace('/', '-', $value)));
    }

    public function get_cms_list_wrapper($value, $class, $id){
        return $this->parent_form->{$this->field_name} = date('d/m/Y @h:i:s', strtotime($value));
    }
}
