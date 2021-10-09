<?php

namespace module\cms\view;

use classes\get;
use html\node;
use module\cms\controller;
use module\cms\model\_cms_table_list;

class module extends cms_view {

    public function get_view(): string {
        $list = new _cms_table_list($this->schema, $this->module->page);
        return node::create('div', [], node::create('h2.page-header.container-fluid', [], 'View all ' . ucwords($this->schema->table_name) . 's') .  $list->get_table());
    }
}
