<?php
namespace form;
class field_hidden extends field {
    public function __construct($title, $options = array()) {
        parent::__construct($title, $options);
        $this->hidden = true;
        $this->attributes['type'] = 'hidden';
    }
}