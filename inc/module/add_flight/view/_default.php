<?php
namespace module\add_flight\view;

use classes\view;

class _default extends \template\html {

    public function get_view() {
        $html = $this->module->page_object->body;
        return $html;
    }
}
