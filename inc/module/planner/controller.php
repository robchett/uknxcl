<?php

namespace module\planner;

use classes\jquery;
use classes\module;
use module\planner\model\declaration;
use module\planner\view\_default;

class controller extends module {

    public string $import_string = '';

    /** @param string[] $path */
    public function __construct(array $path) {
        $this->view_object = new _default($this, false);
        if (isset($path[1])) {
            $this->import_string = (string) filter_var(urldecode($path[1]), FILTER_SANITIZE_STRING);
        }
        parent::__construct($path);
    }

    public static function get_form(): void {
        if (isset($_REQUEST['ftid']) && isset($_REQUEST['coordinates'])) {
            $form = declaration::get_form();
            jquery::colorbox(['html' => $form->get_html()]);
        }
    }
}
