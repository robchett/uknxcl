<?php
class module_view extends cms_view {
    public function get_view() {
        $html = html_node::create('div')->nest(array(
                html_node::create('h2', 'View all ' . get_class($this->module->current) . 's'),
                $this->module->get_inner(),
            )
        );
        return $html;
    }
}
