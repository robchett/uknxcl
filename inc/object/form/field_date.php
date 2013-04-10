<?php

class field_date extends field {
    public function __construct($title, $options = array()) {
        parent::__construct($title, $options);
        $this->attributes['pattern'] = '[0-9]{2}/[0-9]{2}/[0-9]{4}';
    }

    public function set_value($val) {
        $this->parent_form->{$this->field_name} = date('d/m/Y', strtotime($val));
    }

    public function mysql_value($value) {
        return date('Y-m-d', strtotime(str_replace('/', '-', $value)));
    }

    public function get_html() {
        if(isset($this->parent_form->{$this->field_name})) {
            $this->parent_form->{$this->field_name} = date('d/m/Y', strtotime($this->parent_form->{$this->field_name}));
            return parent::get_html();
        }
    }

}
