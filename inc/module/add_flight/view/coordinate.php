<?php

namespace module\add_flight\view;

use classes\get;
use module\add_flight\form\coordinates_form;
use template\html;

class coordinate extends html {

    public function get_page_selector(): string {
        return get::__namespace($this->module, 0) . '-coordinate';
    }

    function get_view(): string {
        $form1 = new coordinates_form();
        return "
<div class='add_flight_section coordinate'>
    <div class='callout callout-primary'>
        {$form1->get_html()}
    </div>
    <a class='back button' href='/add_flight'>Back</a>
</div>";
    }
}
