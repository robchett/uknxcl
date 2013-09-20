<?php
namespace form;
class field_password extends field {
    public function __construct($title, $options = array()) {
        parent::__construct($title, $options);
        $this->attributes['type'] = 'password';
    }
}
