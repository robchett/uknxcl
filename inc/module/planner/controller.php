<?php
namespace module\planner;

use classes\compiler;
use classes\jquery;
use classes\module;

class controller extends module {

    public $page = 'planner';
    public $import_string = '';

    public function __controller(array $path) {
        if(isset($path[1])) {
            $this->import_string = filter_var($path[1], FILTER_SANITIZE_STRING);
            compiler::disable();
        }
        parent::__controller($path);
    }

    public function get_form() {
        if (isset($_REQUEST['ftid']) && isset($_REQUEST['coordinates'])) {
            $dec = new object\declaration();
            $form = $dec->get_form();
            jquery::colorbox(['html' => $form->get_html()->get()]);
        }
    }
}
