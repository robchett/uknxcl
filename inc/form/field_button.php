<?php

namespace form;

use classes\attribute_callable;
use classes\icon;
use html\node;
use RuntimeException;

class field_button extends field {

    /** @var string */
    public string $title;

    public function __construct($title = '', $options = []) {
        parent::__construct($title, $options);
    }

    public function get_database_create_query(): ?string {
        return null;
    }

    public function get_cms_list_wrapper($value, $object_class, $id): string {
        $this->attributes['data-ajax-click'] = attribute_callable::create([$object_class, $this->field_name]);
        $this->attributes['data-ajax-post'] = '{"id":' . $id . '}';
        $this->attributes['data-ajax-shroud'] = '#button' . $this->field_name . $id;
        return node::create('a#button_' . $this->field_name . $id . '.btn.btn-default', $this->attributes, icon::get($this->field_name));
    }

    public function get_save_sql() {
        throw new RuntimeException('Can\t save this field type');
    }

    public function get_html_wrapper(): ?string {
        return null;
    }
}
