<?php
namespace module\flight_info\view;

use traits\twig_view;

class _default extends \template\html {
    use twig_view;

    function get_template_data() {
        return [
            'content' => $this->module->page_object->body
        ];
    }
}
