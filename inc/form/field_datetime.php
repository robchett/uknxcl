<?php

namespace form;

use JetBrains\PhpStorm\Pure;

class field_datetime extends field {

    public function __construct($title, $options = []) {
        parent::__construct($title, $options);
        $this->attributes['pattern'] = '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}';
    }

    #[Pure]
    public static function sanitise_from_db($value): bool|int {
        return strtotime($value);
    }

    public function set_value($val) {
        $this->parent_form->{$this->field_name} = date('d/m/Y h:i:s', strtotime($val));
    }

    public function get_html(): string {
        $attributes = $this->attributes;
        $this->set_standard_attributes($attributes);
        return '<input ' . static::get_attributes($attributes) . ' value="' . date('d/m/Y h:i:s', strtotime($this->parent_form->{$this->field_name})) . '"/>';
    }

    public function mysql_value($value): bool|string {
        return date('Y-m-d h:i:s', strtotime(str_replace('/', '-', $value)));
    }

    #[Pure]
    public function get_cms_list_wrapper($value, $object_class, $id): bool|string {
        return date('d/m/Y @h:i:s', strtotime($value));
    }
}
