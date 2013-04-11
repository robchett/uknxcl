<?php

class add_field_form extends form {

    public function __construct() {
        parent::__construct([
                form::create('field_string', 'title'),
                form::create('field_string', 'field_name'),
                form::create('field_link', 'type')->set_attr('link_module',16)->set_attr('link_field',79),
                form::create('field_int', 'mid'),
            ]
        );

        $this->get_field_from_name('mid')->set_attr('hidden', true);
    }

    public function do_submit() {
        if (parent::do_submit()) {
            $module = db::result('SELECT * FROM _cms_modules WHERE mid=:mid', array('mid' => $this->mid));
            $field = new field_type();
            $field->do_retrieve_from_id(array(), $this->type);
            $type = 'field_' . $field->title;
            $field_type = new $type('');
            if($inner = $field_type->get_database_create_query()) {
                db::query('ALTER TABLE ' . $module->table_name . ' ADD ' . $this->field_name . ' ' . $field_type->get_database_create_query(), array(), 1);
            }
            db::query('INSERT INTO _cms_fields SET title=:title, type=:type, field_name=:field_name, mid=:mid', array(
                    'title' => $this->title,
                    'field_name' => $this->field_name,
                    'type' => $field->title,
                    'mid' => $this->mid,
                )
            );
        }
        ajax::add_script('window.location = window.location');
    }
}
