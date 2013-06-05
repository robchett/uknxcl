<?php
class edit_view extends cms_view {
    public function get_view() {
        $html = html_node::create('div')->nest(array(
                html_node::create('h2', 'Edit a ' . get_class($this->module->current)),
                $this->module->get_admin_edit(),
                $this->module->current->get_cms_edit(),
            )
        );
        return $html;
    }
}