<?php

namespace module\cms\view;

use html\node;
use module\cms\controller;

class edit extends cms_view {

    public function get_view(): string {
        if (!$this->current) {
            return 'Item not found';
        }
        return node::create('div', [], 
            node::create('h2.container-fluid.page-header', [], 'Edit a ' . $this->schema->table_name) . 
            ($this->current->is_deleted() ? node::create('div.container div.bs-callout.bs-callout-warning p', [], 'This element is deleted') : '') . 
            (!$this->current->is_live() && $this->current->get_primary_key() ? node::create('div.container div.bs-callout.bs-callout-warning p', [], 'This element is not live') : '') . 
            $this->current->get_cms_edit()
        );
    }
}