<?php
namespace module\converter\view;

use classes\view;
use html\node;
use module\converter\form;

class dashboard extends view {

    public function get_view() {
        $html = node::create('div', [],
            node::create('h2', [], 'UKNXCL Conversion Tools') .
            $this->get_coordinate_converter()
        );
        return $html;
    }

    public function get() {
        return $this->get_view()->get();
    }

    public function get_coordinate_converter() {
        $form = new form\coordinate_conversion_form();
        return $form->get_html();
    }
}
