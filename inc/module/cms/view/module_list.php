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
        $modules = _cms_modules::get_all(array('mid', 'title'));
        if ($modules) {
            $ul = html_node::create('ul');
            $modules->iterate($t = function ($module) use ($ul) {
                    $ul->nest(html_node::create('li', $module->mid . '-' . $module->title));
                }
            );
            $html->nest($ul);
        }
        return $html;
    }
}
