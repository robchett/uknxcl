<?php
namespace module\comps\view;

use classes\error_handler;
use traits\twig_view;

class comp extends \template\html {
    use twig_view;

    /** @vat \module\comps\controller */
    public $module;

    function get_template_file() {
        return 'inc/module/comps/view/comp.twig';
    }

    function get_template_data() {
        $file = file_get_contents($this->module->current->get_js_file());
        $data = json_decode($file) || [];
        if ($data === []) {
            error_handler::debug('Json decode error', ['message' => json_last_error_msg()]);
        }
        return $data;
    }

    public function get_js() {
        return "map.callback(function(map) {map.add_comp(" . $this->module->current->cid . ")});";
    }

    public function get_js_ajax() {
        return 'map.add_comp(' . $this->module->current->cid . ');';
    }
}
 