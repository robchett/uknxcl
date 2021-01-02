<?php

namespace module\cms\view;

use html\node;
use module\cms\controller;

class new_module extends cms_view {

    /** @var controller */
    public \classes\module $module;

    public function get_view(): string {
        return node::create('div.container-fluid', [], node::create('h2.page-header', [], 'New Module') . "<p>Create a new module and nest it under a group.<p>" . $this->module->get_admin_new_module_form());
    }
}
