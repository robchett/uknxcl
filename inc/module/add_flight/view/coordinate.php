<?php
namespace module\add_flight\view;

use classes\ajax;
use classes\get;
use classes\view;
use html\node;
use module\add_flight\form\coordinates_form;

class coordinate extends \template\html {

    public function get_page_selector() {
        return get::__namespace($this->module, 0) . '-coordinate';
    }

    public function get_view() {
        $form = new coordinates_form();
        $form->wrapper_class[] = 'callout';
        $form->wrapper_class[] = 'callout-primary';
        $html = node::create('div.add_flight_section.coordinate', [],
            $form->get_html() .
            node::create('a.back.button', ['href' => '/add_flight'], 'Back')
        );
        return $html;
    }
}
