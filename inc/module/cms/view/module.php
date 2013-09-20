<?php
namespace cms;
use html\node;

class module_view extends cms_view {
    public function get_view() {
        $html = node::create('div')->nest([
                node::create('h2', 'View all ' . get_class($this->module->current) . 's'),
                $this->module->get_inner(),
            ]
        );
        return $html;
    }
}
