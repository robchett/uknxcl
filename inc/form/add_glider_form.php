<?php

namespace form;

use classes\ajax;
use classes\jquery;
use object\glider;
use object\manufacturer;

class add_glider_form extends form {

    public $mid;
    public $name;

    public function __construct() {
        $this->glider = new glider();
        parent::__construct($this->glider->get_fields());
        $this->get_field_from_name('gid')
            ->set_attr('hidden', true)
            ->set_attr('required', false);
        $this->get_field_from_name('name')->set_attr('label', 'Name');
        $this->get_field_from_name('mid')
            ->set_attr('label', 'Manufacturer')
            ->set_attr('options', ['order' => 'title ASC']);
        $this->get_field_from_name('class')
            ->set_attr('label', 'Class')
            ->set_attr('options', [1 => 1, 5 => 5]);
        $this->get_field_from_name('kingpost')->set_attr('label', 'Has kingpost?');
        $this->get_field_from_name('single_surface')->set_attr('label', 'Is single surface?');
        $this->id = 'new_glider_form';
        $this->h2 = 'Add a new glider';
    }

    public function get_form() {
        jquery::colorbox(['html'=>$this->get_html()->get()]);
    }

    public function do_submit() {
        $this->glider->set_from_request();
        $this->glider->name = ucwords($this->name);
        $this->glider->do_save();
        $manu = new manufacturer();
        $manu->do_retrieve_from_id(['title'], $this->mid);
        if ($this->glider->gid) {
            $this->glider->do_update_selector();
            jquery::colorbox(["html" => $manu->title . ' - ' . $this->glider->name . ' has been added to the database and should now be selectable from the list.']);
        }
    }
}