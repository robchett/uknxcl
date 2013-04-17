<?php

class comp_convert extends form {

    public function __construct() {
        $fields = array(
            'pilot' => form::create('field_link', 'pilot')
                ->set_attr('label', 'Pilot')
                ->set_attr('link_module', 'pilot')
                ->set_attr('link_field', 'pid'),
            'glider' => form::create('field_select', 'glider')
                ->set_attr('label', 'Glider')
                ->set_attr('link_module', 'glider')
                ->set_attr('link_field', 'gid'),
            'club' => form::create('field_select', 'club')
                ->set_attr('label', 'Club')
                ->set_attr('link_module', 'club')
                ->set_attr('link_field', 'cid'),
            'launch' => form::create('field_select', 'launch')
                ->set_attr('options', flight::$launch_types)
                ->set_attr('label', 'Launch'),
            'file' => form::create('field_hidden', 'file'),
            'vis_info' => form::create('field_hidden', 'vis_info'),
            'invis_info' => form::create('field_hidden', 'invis_info'),
            'comp' => form::create('field_hidden', 'comp'),
        );
        parent::__construct($fields);
    }
}