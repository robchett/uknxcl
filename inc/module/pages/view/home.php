<?php
namespace module\pages\view;

use module\pages\object\page;
use traits\twig_view;

/** @property \module\pages\controller $module */
class home extends \core\module\pages\view\_default {
    use twig_view;

    public function get_template_data() {
        $pages = page::get_all(['title', 'info', 'module_name', 'fn', 'icon'], ['order' => 'position', 'where' => 'pid != 12']);
        return [
            'pages' => $pages->get_template_data()
        ];
    }
}
