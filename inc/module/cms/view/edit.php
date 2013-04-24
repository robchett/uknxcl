<?php
$html = html_node::create('div')->nest(array(
        html_node::create('h2', 'Welcome to the dashboard'),
        html_node::create('p', 'From here you will able to edit pretty much anything on the site, currently a work in progress so only the parts below will be editable for now'),
        $this->get_admin_edit(),
        $this->current->get_cms_edit(),
    )
);

echo $html->get();
