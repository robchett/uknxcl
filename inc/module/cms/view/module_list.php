<?php
class module_list_view extends cms_view {
    public function get_view() {
        $html = html_node::create('div')->nest(array(
                html_node::create('h2', 'Module List'),
                html_node::create('p', 'Manage your modules from here'),
                $this->get_module_list()
            )
        );
        return $html;
    }

    public function get_module_list() {
        $html = html_node::create('div');
        $modules = _cms_modules::get_all(array('mid', 'title','primary_key', '_cms_group.title', 'table_name'), array('join'=>array('_cms_group'=>'_cms_group.gid = _cms_modules.gid')));
        if ($modules) {
            $table = html_node::create('table.module');
            $table->nest(
                html_node::create('thead')->nest(
                    array(
                        html_node::create('th', 'Module ID'),
                        html_node::create('th', 'Group'),
                        html_node::create('th', 'Title'),
                        html_node::create('th', 'Table Name'),
                        html_node::create('th', 'Primary Key'),
                    )
                )
            );
            $modules->iterate($t = function ($module) use ($table) {
                    $table->nest(
                        html_node::create('tr')->nest(
                            array(
                                html_node::create('td', $module->mid),
                                html_node::create('td', $module->_cms_group_title),
                                html_node::create('td', $module->title),
                                html_node::create('td', $module->table_name),
                                html_node::create('td', $module->primary_key),
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
