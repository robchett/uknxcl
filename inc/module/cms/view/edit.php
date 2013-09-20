<?php
namespace cms;
use html\node;

class edit_view extends cms_view {
    public function get_view() {
        $html = node::create('div')->nest(array(
                node::create('h2', 'Edit a ' . get_class($this->module->current)),
                $this->module->current->get_cms_edit(),
            )
        );
        return $html;
    }
}