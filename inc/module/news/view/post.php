<?php
namespace module\news\view;

use module\news;
use traits\twig_view;

class post extends \template\html {
    use twig_view;


    /** @var \module\news\controller $module */
    public $module;

    public function get_template_data() {
        return [
            'article' => $this->module->current->get_template_data()
        ];
    }
}
