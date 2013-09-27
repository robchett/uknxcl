<?php
namespace cms;

use form\form;

/**
 * @property mixed field_name
 */
class add_field_form extends form {

    public $mid;
    public $title;
    public $type;

    public function __construct() {
        parent::__construct(array(
                form::create('field_string', 'title'),
                form::create('field_string', 'field_name'),
                form::create('field_link', 'type')->set_attr('link_module', 16)->set_attr('link_field', 79),
                form::create('field_int', 'mid')
            )
        );

        $this->get_field_from_name('mid')->set_attr('hidden', true);
    }

    public function do_submit() {
        if (parent::do_submit()) {
            $module = new _cms_modules([], $this->mid);
            $field = new field_type([], $this->type);
            $type = '\form\field_' . $field->title;
            /** @var \form\field $field_type */
            $field_type = new $type('');
            if ($inner = $field_type->get_database_create_query()) {
                \db::query('ALTER TABLE ' . $module->table_name . ' ADD ' . $this->field_name . ' ' . $field_type->get_database_create_query(), array(), 1);
            }
            $res = \db::result('SELECT MAX(position) AS pos FROM _cms_fields WHERE mid=:mid', array('mid' => $this->mid));
            \db::insert('_cms_fields')
                ->add_value('title', $this->title)
                ->add_value('type', $field->title)
                ->add_value('field_name', $this->field_name)
                ->add_value('mid', $this->mid)
                ->add_value('position', $res->pos + 1)
                ->execute();
            $obj = $module->get_class();
            $obj->mid = $this->mid;
            \ajax::update($obj->get_cms_edit_module()->get());
        }
        \ajax::update($this->get_html()->get());
    }
}
