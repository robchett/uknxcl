<?php
namespace module\flight_info\view;

use classes\view;

class _default extends \template\html {

    public function get_view() {
        return $this->module->page_object->body;
    }
}
