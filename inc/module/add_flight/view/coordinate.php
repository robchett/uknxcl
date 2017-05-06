<?php
namespace module\add_flight\view;

use classes\ajax;
use classes\get;
use classes\view;
use html\node;
use module\add_flight\form\coordinates_form;
use traits\twig_view;

class coordinate extends \template\html {
    use twig_view;

    public function get_page_selector() {
        return get::__namespace($this->module, 0) . '-coordinate';
    }

    function get_template_data() {
        $form1 = new coordinates_form();
        return [
            'form_1' => $form1->get_html()->get(),
        ];
    }
}
