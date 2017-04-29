<?php
namespace module\add_flight\view;

use classes\get;
use module\add_flight\form\igc_form;
use module\add_flight\form\igc_upload_form;
use traits\twig_view;

class igc_supported extends \template\html {
    use twig_view;

    public function get_page_selector() {
        return get::__namespace($this->module, 0) . '-igc';
    }

    function get_template_file() {
        return 'inc/module/add_flight/view/igc_supported.twig';
    }

    function get_template_data() {
        $form1 = new igc_form();
        $form2 = new igc_upload_form();
        return [
            'form_1' => $form1->get_html()->get(),
            'form_2' => $form2->get_html()->get(),
        ];
    }
}
