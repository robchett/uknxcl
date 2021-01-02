<?php

namespace form;

use html\node;

class field_textarea extends field {

    public function get_html(): string {
        $attributes = $this->attributes;
        $this->set_standard_attributes($attributes);
        return "<textarea $attributes>" . htmlentities($this->parent_form->{$this->field_name}) . "</textarea>\n";
    }

    public function get_database_create_query(): string {
        return 'TEXT';
    }

    public function get_cms_list_wrapper($value, $object_class, $id): string {
        return node::create('div.well.well-small.auto-collapse', ['data-collapse-height' => "200px"], $value);
    }
}
