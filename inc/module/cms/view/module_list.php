<?php

namespace module\cms\view;

use html\node;
use module\cms\model\_cms_module;

class module_list extends cms_view {

    public function get_view(): string {
        return node::create('div.container-fluid', [], node::create('h2.page-header', [], 'Module List') . "<p>Manage your modules from here<p>" . $this->get_module_list());
    }

    public function get_module_list(): string {
        $modules = _cms_module::get_all(['mid', 'title', 'primary_key', '_cms_group.title', 'table_name'], ['join' => ['_cms_group' => '_cms_group.gid = _cms_module.gid']]);
        if ($modules) {
            return node::create('div', [], node::create('table.module.table.table-striped', [], "<thead><th>Module ID</th><th>Group</th><th>Title</th><th>Table Name</th><th>Primary Key<th><thead>" . $modules->iterate_return(function (_cms_module $module) {
                    $attributes = ['href' => '/cms/admin_edit/' . $module->mid];
                    return node::create('tr', [], node::create('td a', $attributes, $module->mid) . node::create('td a', $attributes, $module->_cms_group->title) . node::create('td a', $attributes, $module->title) . node::create('td a', $attributes, $module->table_name) . node::create('td a', $attributes, $module->primary_key));
                })

            ));
        }
        return '';
    }
}
