<?php
namespace module\converter\view;

use classes\view;
use html\node;
use module\converter\form;
use traits\twig_view;

class _default extends \template\html {
    use twig_view;

    function get_template_data() {
        $form1 = new form\coordinate_conversion_form();
        return [
            'form_1' => $form1->get_html()->get(),
        ];
    }
}
