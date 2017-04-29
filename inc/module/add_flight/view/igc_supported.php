<?php
namespace module\add_flight\view;

use classes\ajax;
use classes\get;
use classes\view;
use html\node;
use module\add_flight\form\igc_form;
use module\add_flight\form\igc_upload_form;

class igc_supported extends \template\html {

    public function get_page_selector() {
        return get::__namespace($this->module, 0) . '-igc';
    }

    public function get_view() {
        $form = new igc_form();
        $form->wrapper_class[] = 'callout';
        $form->wrapper_class[] = 'callout-primary';
        $form2 = new igc_upload_form();
        $form2->wrapper_class[] = 'callout';
        $form2->wrapper_class[] = 'callout-primary';
        $html = node::create('div.add_flight_section.upload', [],
            $form2->get_html()->get() .
            $form->get_html()->get() .
            node::create('a.back.button', ['href' => '/add_flight'], 'Back')
        );
        return $html;
    }
}
