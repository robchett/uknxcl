<?php

namespace form;

use classes\interfaces\model_interface;
use classes\table;
use html\node;

/**
 * @extends field<string>
 */
class field_textarea extends field {

    public function get_html(form $form): string {
        $attributes = $this->set_standard_attributes($this->attributes);
        return "<textarea " . $attributes . ">" . htmlentities($this->get_value($form)) . "</textarea>\n";
    }

    public function get_cms_list_wrapper(model_interface $form, mixed $value, string $object_class, int $id): string {
        return node::create('div.well.well-small.auto-collapse', ['dataCollapseHeight' => "200px"], $value);
    }
}
