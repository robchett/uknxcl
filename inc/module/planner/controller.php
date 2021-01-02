<?php

namespace module\planner;

use classes\jquery;
use classes\module;

class controller extends module {

    public string $import_string = '';

    public function __controller(array $path) {
        if (isset($path[1])) {
            $this->import_string = filter_var(urldecode($path[1]), FILTER_SANITIZE_STRING);
        }
        parent::__controller($path);
    }

    public static function get_form() {
        if (isset($_REQUEST['ftid']) && isset($_REQUEST['coordinates'])) {
            $dec = new model\declaration();
            $form = $dec->get_form();
            jquery::colorbox(['html' => (string)$form->get_html()]);
        }
    }
}
