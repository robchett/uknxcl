<?php

namespace form;

use JetBrains\PhpStorm\Pure;

class field_date extends field {

    public function __construct($title, $options = []) {
        parent::__construct($title, $options);
        //$this->attributes['pattern'] = '[0-9]{2}/[0-9]{2}/[0-9]{4}';
        $this->attributes['type'] = 'date';
    }

    #[Pure]
    public static function sanitise_from_db($value): bool|int {
        return @strtotime($value);
    }

    public function set_value($val) {
        $this->parent_form->{$this->field_name} = strtotime($val);
    }

    public function set_from_request() {
        $this->parent_form->{$this->field_name} = isset($_REQUEST[$this->field_name]) ? strtotime($_REQUEST[$this->field_name]) : '';
    }

    #[Pure]
    public function get_cms_list_wrapper($value, $object_class, $id): bool|string {
        return date('d-m-Y', $value);
    }

    #[Pure]
    public function mysql_value($value): bool|string {
        if (!is_numeric($value)) {
            $value = strtotime($value);
        }
        return date('Y-m-d', $value);
    }

    #[Pure]
    public function get_value(): bool|string {
        $value = (float)$this->parent_form->{$this->field_name};
        return $value ? date('Y-m-d', (float)$this->parent_form->{$this->field_name}) : '';
    }

}
