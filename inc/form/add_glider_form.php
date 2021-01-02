<?php

namespace form;

use classes\jquery;
use html\node;
use model\glider;
use model\manufacturer;

class add_glider_form extends form {

    public int $mid;
    public string $name;
    public glider $glider;

    public function __construct() {
        $this->glider = new glider();
        parent::__construct($this->glider->get_fields());
        $this->get_field_from_name('gid')->set_attr('hidden', true)->set_attr('required', false);
        $this->get_field_from_name('name')->set_attr('label', 'Name');
        $this->get_field_from_name('mid')->set_attr('label', 'Manufacturer')->set_attr('options', ['order' => 'title ASC']);
        $this->get_field_from_name('class')->set_attr('label', 'Class')->set_attr('options', [1 => 1, 5 => 5]);
        $this->get_field_from_name('kingpost')->set_attr('label', 'Has kingpost?');
        $this->get_field_from_name('single_surface')->set_attr('label', 'Is single surface?');
        $this->id = 'new_glider_form';
        $this->h2 = 'Add a new glider';
        $this->attributes['class'][] = 'form-compact';
        $this->wrapper_class[] = 'callout';
        $this->wrapper_class[] = 'callout-primary';
    }

    public static function get_form() {
        $t = new static();
        jquery::colorbox(['html' => (string)$t->get_html()]);
    }

    public function do_submit(): bool {
        $this->glider->set_from_request();
        $this->glider->name = ucwords($this->name);
        $this->glider->do_save();
        $manu = new manufacturer();
        $manu->do_retrieve_from_id(['title'], $this->mid);
        if ($this->glider->gid) {
            $this->glider->do_update_selector();
            jquery::colorbox(["html" => (string)node::create('div.callout.callout-primary', [], node::create('h2.page-header', [], $manu->title . ' - ' . $this->glider->name) . "<p>Has been added to the database and should now be selectable from the list.<p>")]);
        }
        return true;
    }
}