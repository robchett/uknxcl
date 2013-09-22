<?php
namespace cms;

use html\node;

class new_module_view extends cms_view {
    public function get_view() {
        $html = node::create('div', [],
            node::create('h2', [], 'New Module') .
            node::create('p', [], 'Create a new module and nest it under a group.') .
            $this->module->get_admin_new_module_form()
        );
        return $html;
    }
}
