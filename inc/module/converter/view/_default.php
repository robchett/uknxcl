<?php
namespace module\converter\view;

use classes\view;
use html\node;
use module\converter\form;

class _default extends \template\html {

    public function get_view() {
        $html = node::create('div', [],
            node::create('h1.page-header', [], 'UKNXCL Conversion Tools') .
            node::create('p', [], 'Enter lat/lng values as decimal or space separated for seconds') .
            $this->get_coordinate_converter()
        );
        return $html;
    }

    public function get_coordinate_converter() {
        $form = new form\coordinate_conversion_form();
        $form->wrapper_class[] = 'callout';
        $form->wrapper_class[] = 'callout-primary';
        return $form->get_html();
    }
}
