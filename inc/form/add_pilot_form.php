<?php

namespace form;

use classes\jquery;
use html\node;
use model\pilot;

class add_pilot_form extends form {

    public int $bhpa_no;
    public string $email;
    public string $name;
    public string $gender;
    public string $rating;
    public pilot $pilot;

    public function __construct() {
        $this->pilot = new pilot();
        parent::__construct($this->pilot->get_fields());
        $this->get_field_from_name('pid')->set_attr('hidden', true)->set_attr('required', false);
        $this->get_field_from_name('name')->set_attr('label', 'Name')->set_attr('required', true);
        $this->get_field_from_name('bhpa_no')->set_attr('label', 'BHPA Number')->set_attr('required', true);
        $this->get_field_from_name('email')->set_attr('label', 'Email');
        $this->get_field_from_name('gender')->set_attr('label', 'Gender')->set_attr('options', ['M' => 'Male', 'F' => 'Female']);
        $this->get_field_from_name('rating')->set_attr('label', 'Rating')->set_attr('options', [1 => 'Club', 2 => 'Advanced']);

        $this->id = 'new_pilot_form';
        $this->h2 = 'Create a new pilot';
        $this->attributes['class'][] = 'form-compact';
        $this->wrapper_class[] = 'callout';
        $this->wrapper_class[] = 'callout-primary';
    }

    public static function get_form() {
        $t = new static();
        jquery::colorbox(['html' => (string)$t->get_html()]);
    }

    public function do_submit(): bool {
        $this->pilot->name = ucwords($this->name);
        $this->pilot->gender = $this->gender;
        $this->pilot->bhpa_no = $this->bhpa_no;
        $this->pilot->email = $this->email;
        $this->pilot->rating = $this->rating;
        $this->pilot->do_save();
        if ($this->pilot->pid) {
            $this->pilot->do_update_selector();
        }
        jquery::colorbox(['html' => (string)node::create('div.callout.callout-primary', [], node::create('h2.page-header', [], $this->name) . "<p>Added to the database<p>")]);
        return true;
    }
}