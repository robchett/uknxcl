<?php
$html = html_node::create('div')->nest([
        html_node::create('h2', 'Welcome to the dashboard'),
        html_node::create('p', 'From here you will able to edit pretty much anything on the site, currently a work in progress so only the parts below will be editable for now'),
        $this->get_admin_edit(),
        $this->get_admin_add(),
        $this->get_inner(),
    ]
);

echo $html->get();
