<?php
namespace module\add_flight\view;

use classes\view;

class _default extends view {

    public function get_view() {
        $html = $this->module->page_object->body;
        return $html;
    }
}
