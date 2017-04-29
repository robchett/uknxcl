<?php
namespace module\flight_info\view;

use traits\twig_view;

class _default extends \template\html {
    use twig_view;

    function get_template_file() {
        return 'inc/module/flight_info/view/_default.twig';
    }

    function get_template_data() {
        return [
            'content' => $this->module->page_object->body
        ];
    }
}
