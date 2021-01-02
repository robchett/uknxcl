<?php

namespace form;

class field_email extends field {

    public function __construct($name, $options = []) {
        parent::__construct($name, $options);
        $this->attributes['type'] = 'email';
    }
}
