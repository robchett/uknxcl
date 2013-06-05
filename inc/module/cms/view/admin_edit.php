<?php
class admin_edit_view extends cms_view {
    public function get_view() {
        $html = html_node::create('div')->nest(array(
                $this->module->current->get_cms_edit_module(),
                $this->module->get_new_field_form()
            )
        );
        return $html;
    }
}
