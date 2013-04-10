<?php
$html = html_node::create('div')->nest([
        html_node::create('h2', 'New Module'),
        html_node::create('p', 'Create a new module and nest it under a group.'),
        $this->get_admin_new_module_form(),
    ]
);

echo $html->get();
