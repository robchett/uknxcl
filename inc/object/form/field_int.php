<?php

class field_int extends field {
    public function __construct($title = '', $options = array()) {
        parent::__construct($title, $options);
        $this->attributes['type'] = 'number';
    }

    public function get_database_create_query(){
        return 'int(32)';
    }
}
