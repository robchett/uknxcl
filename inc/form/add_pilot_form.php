<?php

class add_pilot_form extends form\form {

    public function __construct() {
        $this->pilot = new pilot();
        parent::__construct($this->pilot->get_fields());
        $this->get_field_from_name('pid')
            ->set_attr('hidden', true)
            ->set_attr('required', false);
        $this->get_field_from_name('name')
            ->set_attr('label', 'Name')
            ->set_attr('required', true);
        $this->get_field_from_name('bhpa_no')
            ->set_attr('label', 'BHPA Number')
            ->set_attr('required', true);
        $this->get_field_from_name('email')->set_attr('label', 'Email');
        $this->get_field_from_name('gender')
            ->set_attr('label', 'Gender')
            ->set_attr('options', array('M' => 'Male', 'F' => 'Female'))
            ->set_attr('required', true);
        $this->get_field_from_name('rating')
            ->set_attr('label', 'Rating')
            ->set_attr('options', array(1 => 'Club', 2 => 'Advanced'))
            ->set_attr('required', true);

        $this->id = 'new_pilot_form';
        $this->h2 = 'Create a new pilot';
    }

    public function get_form() {
        jquery::colorbox(['html' => $this->get_html()]);
    }

    public function do_submit() {
        if (parent::do_submit()) {
            $this->pilot->name = ucwords($this->name);
            $this->pilot->gender = $this->gender;
            $this->pilot->bhpa_no = $this->bhpa_no;
            $this->pilot->email = $this->email;
            $this->pilot->rating = $this->rating;
            $this->pilot->do_save();
            if ($this->pilot->pid) {
                $this->pilot->do_update_selector();
            }
            jquery::colorbox('<strong>' . $this->name . '</strong>' . ' added to the database');
        }
    }
}