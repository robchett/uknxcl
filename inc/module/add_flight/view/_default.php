<?php
namespace module\add_flight\view;

use html\node;

class _default extends \template\html {

    public function get_view() {
        $html = node::create('div.editable_content', [], $this->module->page_object->body);
        return $html;
    }
}
