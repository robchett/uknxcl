<?php
namespace module\stats\view;

use html\node;
use traits\twig_view;

class _default extends \template\html {
    use twig_view;

    /** @var \module\stats\controller $module */
    public $module;

    /** @return node */
    public function get_template_data() {
        $stats = $this->module->get_stats();
        return [
            'body' => $this->module->page_object->body,
            'stats' => $stats,
        ];
    }
}
