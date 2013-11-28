<?php

class comp_convert extends form\form {

    public function __construct() {
        $fields = [
            'pilot' => form\form::create('field_link', 'pilot')
                    ->set_attr('label', 'Pilot')
                    ->set_attr('link_module', '\\object\\pilot')
                    ->set_attr('link_field', 'pid'),
            'glider' => form\form::create('field_select', 'glider')
                    ->set_attr('label', 'Glider')
                    ->set_attr('link_module', '\\object\\glider')
                    ->set_attr('link_field', 'gid'),
            'club' => form\form::create('field_select', 'club')
                    ->set_attr('label', 'Club')
                    ->set_attr('link_module', '\\object\\club')
                    ->set_attr('link_field', 'cid'),
            'launch' => form\form::create('field_select', 'launch')
                    ->set_attr('options', \object\flight::$launch_types)
                    ->set_attr('label', 'Launch'),
            'file' => form\form::create('field_hidden', 'file'),
            'vis_info' => form\form::create('field_hidden', 'vis_info'),
            'invis_info' => form\form::create('field_hidden', 'invis_info'),
            'comp' => form\form::create('field_hidden', 'comp'),
        ];
        parent::__construct($fields);
    }
}