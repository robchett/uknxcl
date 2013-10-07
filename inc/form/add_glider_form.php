<?php

namespace form;

class add_glider_form extends form {

    public $mid;
    public $name;

    public function __construct() {
        $this->glider = new \object\glider();
        parent::__construct($this->glider->get_fields());
        $this->get_field_from_name('gid')
            ->set_attr('hidden', true)
            ->set_attr('required', false);
        $this->get_field_from_name('name')->set_attr('label', 'Name');
        $this->get_field_from_name('mid')->set_attr('label', 'Manufacturer');
        $this->get_field_from_name('class')
            ->set_attr('label', 'Class')
            ->set_attr('options', array(1 => 1, 5 => 5));
        $this->get_field_from_name('kingpost')->set_attr('label', 'Has kingpost?');
        $this->get_field_from_name('single_surface')->set_attr('label', 'Is single surface?');
        $this->id = 'new_glider_form';
        $this->h2 = 'Add a new glider';
    }

    public function get_form() {
        \classes\ajax::inject('body', 'after', '<script>$.colorbox({html:' . json_encode($this->get_html()->get()) . '})</script>');
    }

    public function do_submit() {
        parent::do_submit();
        $this->glider->set_from_request();
        $this->glider->name = ucwords($this->name);
        $this->glider->do_save();
        $manu = new \object\manufacturer();
        $manu->do_retrieve_from_id(array('title'), $this->mid);
        if ($this->glider->gid) {
            $this->glider->do_update_selector();
            \classes\jquery::colorbox(array("html" => $manu->title . ' - ' . $this->glider->name . ' has been added to the database and should now be selectable from the list.'));
        }
    }
}