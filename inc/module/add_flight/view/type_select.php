<?php
namespace module\add_flight\view;

use traits\twig_view;

class type_select extends \template\html {
    use twig_view;

    /** @vat \module\comps\controller */
    public $module;

    function get_template_data() {
        return [
            'content' => $this->module->page_object->body
        ];
    }
}
