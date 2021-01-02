<?php

namespace module\cms\view;

use classes\get;
use html\node;
use module\cms\controller;

class module extends cms_view {

    /** @var controller */
    public \classes\module $module;

    public function get_view(): string {
        return node::create('div', [], node::create('h2.page-header.container-fluid', [], 'View all ' . ucwords(get::__class_name($this->module->current)) . 's') . $this->module->get_inner());
    }
}
