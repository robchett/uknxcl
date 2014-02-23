<?php
namespace module\comps\view;

use classes\ajax;
use classes\view;
use html\node;

class comp extends \template\html {

    /** @vat \module\comps\controller */
    public $module;

    /** @return \html\node */
    public function get_view() {
        $html = '';
        $file = file_get_contents(root . '/uploads/comp/' . $this->module->current->cid . '/points.js');
        if ($file) {
            $data = json_decode($file);
            $html .= $data->html;
        }
        $html .= node::create('a.comp_back.button', ['href' => '/comps'], 'Back To List');

        if (!ajax) {
            \core::$inline_script[] = "
            map.callback(function(map) {
                map.add_comp(" . $this->module->current->cid . ");
            }); ";
        } else {
            ajax::add_script('map.add_comp(' . $this->module->current->cid . ');');
        }
        return $html;
    }
}
 