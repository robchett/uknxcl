<?php

namespace module\pages\view;

use classes\module;
use classes\push_state;
use html\node;
use module\pages\controller;
use template\html;

/** @extends html<\module\pages\controller, \module\pages\model\page> */
class _default extends html
{
    public function get_view(): string
    {
        return node::create('div.editable_content', [], $this->current->body);
    }

    public function get_page_selector(): string
    {
        return 'pages-' . $this->current->pid;
    }
}
