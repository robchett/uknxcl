<?php
class admin_edit_view extends cms_view {
    public function get_view() {
        $html = html_node::create('div')->nest(array(
                html_node::create('a.button.change_group','Change Group', array('href' => '#', 'data-ajax-click'=>'_cms_modules:get_cms_change_group', 'data-ajax-post'=>json_encode(array('mid'=>$this->module->module->mid)))),
                $this->module->current->get_cms_edit_module(),
                $this->module->get_new_field_form()
            )
        );
        return $html;
    }
}
