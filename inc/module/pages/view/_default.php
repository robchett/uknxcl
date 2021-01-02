<?php

namespace module\pages\view;

use classes\module;
use html\node;
use module\pages\controller;
use template\html;

class _default extends html {

    /** @var controller */
    public module $module;

    public function get_view(): string {
        return node::create('div.editable_content', [], $this->module->current->body);
    }

    public function get_page_selector(): string {
        return 'pages-' . $this->module->current->pid;
    }
}
