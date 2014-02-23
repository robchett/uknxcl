<?php
namespace module\planner;

use classes\jquery;
use classes\module;

class controller extends module {

    public $page = 'planner';

    public function get_form() {
        if (isset($_REQUEST['ftid']) && isset($_REQUEST['coordinates'])) {
            $dec = new object\declaration();
            $form = $dec->get_form();
            jquery::colorbox(['html' => $form->get_html()->get()]);
        }
    }
}
