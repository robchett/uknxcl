<?php

namespace form;

use JetBrains\PhpStorm\Pure;

class field_password extends field {

    public function __construct($title, $options = []) {
        parent::__construct($title, $options);
        $this->attributes['type'] = 'password';
    }

    #[Pure]
    public function get_save_sql(): string {
        return md5($this->parent_form->{$this->field_name});
    }
}
