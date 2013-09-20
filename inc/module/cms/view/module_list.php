<?php
namespace cms;
use html\node;

class module_list_view extends cms_view {
    public function get_view() {
        $html = node::create('div')->nest([
                node::create('h2', 'Module List'),
                node::create('p', 'Manage your modules from here'),
                $this->get_module_list()
            ]
        );
        return $html;
    }

    public function get_module_list() {
        $html = node::create('div');
        $modules = _cms_modules::get_all(array('mid', 'title', 'primary_key', '_cms_group.title', 'table_name'), array('join' => array('_cms_group' => '_cms_group.gid = _cms_modules.gid')));
        if ($modules) {
            $table = node::create('table.module');
            $table->nest(
                node::create('thead')->nest(
                    array(
                        node::create('th', 'Module ID'),
                        node::create('th', 'Group'),
                        node::create('th', 'Title'),
                        node::create('th', 'Table Name'),
                        node::create('th', 'Primary Key'),
                    )
                )
            );
            $modules->iterate($t = function ($module) use ($table) {
                    $table->nest(
                        node::create('tr')->nest(
                            array(
                                node::create('td', $module->mid),
                                node::create('td', $module->_cms_group_title),
                                node::create('td', $module->title),
                                node::create('td', $module->table_name),
                                node::create('td', $module->primary_key),
                            )
                        )
                    );
                }
            );
            $html->nest($table);
        }
        return $html;
    }
}
