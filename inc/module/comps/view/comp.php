<?php
namespace module\comps\view;

use classes\ajax;
use classes\view;
use html\node;
use traits\twig_view;

class comp extends \template\html {
    use twig_view;

    /** @vat \module\comps\controller */
    public $module;

    function get_template_file() {
        return 'inc/module/comps/view/comp.twig';
    }

    function get_template_data() {
        if (file_exists(root . '/uploads/comp/' . $this->module->current->cid . '/points.js')) {
            rename(root . '/uploads/comp/' . $this->module->current->cid . '/points.js', root . '/uploads/comp/' . $this->module->current->cid . '/comp.js');
        }
        $file = file_get_contents(root . '/uploads/comp/' . $this->module->current->cid . '/comp.js');
        $data = json_decode($file);
        return $data;
    }

    public function get_js() {
        return "map.callback(function(map) {map.add_comp(" . $this->module->current->cid . ")});";
    }

    public function get_js_ajax() {
        return 'map.add_comp(' . $this->module->current->cid . ');';
    }
}
 