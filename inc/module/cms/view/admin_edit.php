<?php
$html = html_node::create('div')->nest([
        $this->current->get_cms_edit_module(),
        $this->get_new_field_form()
    ]
);

echo $html->get();
