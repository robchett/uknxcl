<?php

namespace module\flight_info\view;

use template\html;

/** @extends html<\module\flight_info\controller, false> */
class _default extends html {
    function get_view(): string {
        return $this->module->page_object->body;
    }
}
