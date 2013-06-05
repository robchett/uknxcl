<?php
class edit_view extends cms_view {
    public function get_view() {
        $html = html_node::create('div')->nest(array(
                html_node::create('h2', 'Welcome to the dashboard'),
                html_node::create('p', 'From here you will able to edit pretty much anything on the site, currently a work in progress so only the parts below will be editable for now'),
                $this->module->get_admin_edit(),
                $this->module->current->get_cms_edit(),
            )
        );
        return $html;
    }
}