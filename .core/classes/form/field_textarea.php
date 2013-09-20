<?php
namespace form;
class field_textarea extends field {

    public function get_html() {
        return '<textarea ' . $this->get_attributes() . '>' . htmlentities($this->parent_form->{$this->field_name}) . '</textarea>' . "\n";
    }
}
