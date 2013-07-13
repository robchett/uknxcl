<?php

class coordinates_form extends form {


    public function __construct() {
        parent::__construct(array(
                form::create('field_link', 'pid')
                    ->set_attr('label', 'Pilot:')
                    ->set_attr('required', true)
                    ->set_attr('default', 'Choose A Pilot')
                    ->set_attr('post_text', '<a data-ajax-click="add_pilot_form:get_form">Not in the list? Click here to add a new pilot</a>')
                    ->set_attr('link_module', 'pilot')
                    ->set_attr('link_field', 'name')
                    ->set_attr('options', array('order' => 'name')),
                form::create('field_link', 'gid')
                    ->set_attr('label', 'Glider:')
                    ->set_attr('required', true)
                    ->set_attr('post_text', '<a data-ajax-click="add_glider_form:get_form">Not in the list? Click here to add a new glider</a>')
                    ->set_attr('link_module', 'glider')
                    ->set_attr('link_field', array('manufacturer.title', 'glider.name'))
                    ->set_attr('options', array('join' => array('manufacturer' => 'manufacturer.mid = glider.mid'), 'order' => 'manufacturer.title, glider.name')),
                form::create('field_link', 'cid')
                    ->set_attr('label', 'Club:')
                    ->set_attr('required', true)
                    ->set_attr('link_module', 'club')
                    ->set_attr('link_field', 'title')
                    ->set_attr('options', array('order' => 'title')),
                form::create('field_string', 'coords')
                    ->set_attr('label', 'Flight coordinates')
                    ->set_attr('required_parent', 'defined')
                    ->set_attr('required', true)
                    ->set_attr('pre_text', '<p>Enter the coordinates below in \'XX000000;XX000000\' format, with no ending \';\'</p>')
                    ->add_wrapper_class('cf')
                    ->set_attr('post_text', '<p class="defined_info"></p>'),
                form::create('field_date', 'date')
                    ->set_attr('label', 'Date:')
                    ->set_attr('required', true),
                form::create('field_link', 'lid')
                    ->set_attr('label', 'Launch:')
                    ->set_attr('required', true)
                    ->set_attr('link_module', 'launch_type')
                    ->set_attr('link_field', 'title'),
                form::create('field_boolean', 'ridge')
                    ->set_attr('label', 'The flight was predominantly in ridge lift, so according to the rules will not qualify for multipliers')
                    ->add_wrapper_class('long_text'),
                form::create('field_textarea', 'vis_info')
                    ->set_attr('label', 'Please write any extra information you wish to be made public here')
                    ->set_attr('required', false)
                    ->add_wrapper_class('long_text'),
                form::create('field_textarea', 'admin_info')
                    ->set_attr('label', 'Please write any extra information you wish to be seen by the admin team here')
                    ->set_attr('required', false)
                    ->add_wrapper_class('long_text'),
                form::create('field_boolean', 'delay')
                    ->set_attr('label', 'Publication of the flight should be delayed until it has been inspected by the admin team.'),
                form::create('field_boolean', 'personal')
                    ->set_attr('label', 'Show the flight in your personal log only / the flight was flown outside of the UK'),
                form::create('field_boolean', 'agree')
                    ->set_attr('label', 'The NXCL is free to publish the flight to the public and to be passed on to skywings for publication. The flight has not broken any airspace laws')
                    ->set_attr('required', true),
            )
        );

        $this->h2 = 'Coordinate Flight';
        $this->id = 'coordinate_form';
        $this->name = 'coordinate';
        $this->title = 'Add Flight Form';
    }

    public function do_submit() {
        if (parent::do_submit()) {
            $flight = new flight();
            $flight->set_from_request();
            $flight->dim = 1;

            $month = date('m', strtotime($this->date));
            $flight->winter = ($month == 1 || $month == 2 || $month == 12);

            if (strtotime($this->date) + (30 * 24 * 60 * 60) < time()) {
                $this->force_delay = true;
                $flight->invis_info .= 'delayed as flight is old.';
            }

            $track = new track();
            $track->set_task($this->coords);
            $flight_type = new flight_type();
            $flight_type->do_retrieve(array('ftid', 'multi', 'multi_defined'), array('where_equals' => array('fn' => $track->task->type)));
            $flight->ftid = $flight_type->ftid;
            $flight->multi = (!$this->ridge ? ($this->defined ? $flight_type->multi_defined : $flight_type->multi) : 1);
            $flight->base_score = $track->task->get_distance();
            $flight->coords = $track->task->get_coordinates();
            $flight->score = $flight->base_score * $flight->multi;
            $flight->delayed = $this->force_delay ? true : $this->delay;
            $flight->do_save();

            jquery::colorbox(array('html' => 'Your flight has been added successfully'));
            $form = new coordinates_form();
            ajax::update($form->get_html()->get());

        }
    }

    public function do_validate() {
        if (!$this->agree) {
            $this->validation_errors['agree'] = 'You must agree to the terms to continue';
        }
        if (!empty($this->coords) && !preg_match('/^((h[l-z]|n[a-hj-z]|s[a-hj-z]|t[abfglmqrvw])[0-9]{6};?){2,5}$/i', $this->coords)) {
            $this->validation_errors['coords'] = 'Coordinated are not valid';
        }
        return parent::do_validate();
    }

}

?>



