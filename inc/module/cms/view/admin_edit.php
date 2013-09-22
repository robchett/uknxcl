<?php
namespace cms;

use html\node;

class admin_edit_view extends cms_view {
    /** @var controller $module */
    public $module;

    public function get_view() {
        $html = node::create('div', [],
            node::create('a.button.change_group', ['href' => '#', 'data-ajax-click' => '_cms_modules:get_cms_change_group', 'data-ajax-post' => json_encode(['mid' => $this->module->module->mid])], 'Change Group') .
            $this->module->current->get_cms_edit_module() .
            $this->module->get_new_field_form()
        );
        return $html;
    }
}
