<?php

class igc_form extends form {


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
                    ->set_attr('link_field', 'name')
                    ->set_attr('options', array('order' => 'name')),
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
                form::create('field_textarea', 'invis_info')
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

                form::create('field_int', 'temp_id')
                    ->set_attr('required', true)
                    ->set_attr('hidden', true),
                form::create('field_string', 'type')
                    ->set_attr('required', true)
                    ->set_attr('hidden', true),
                form::create('field_int', 'start')
                    ->set_attr('required', true)
                    ->set_attr('hidden', true),
                form::create('field_int', 'end')
                    ->set_attr('required', true)
                    ->set_attr('hidden', true),
                form::create('field_boolean', 'defined')
                     ->set_attr('hidden', true)
            )
        );

        $this->h2 = 'Additional Detials';
        $this->id = 'igc_form';
        $this->name = 'igc';
        $this->title = 'Add Flight Form';
        if (!ajax) {
            $this->submittable = false;
        }
    }

    public function do_submit() {
        if (parent::do_submit()) {
            $flight = new flight();
            $flight->set_from_request();
            $flight->do_save();

            if($flight->fid) {
                track::move_temp_files ($this->temp_id, $flight->fid);
                $track = new track();
                $track->id = $flight->fid;
                $track->parse_IGC();
                $track->truncate($this->start, $this->end);
                $track->generate($flight);

                $flight->date = $track->get_date();
                $flight->did = $track->get_dim();
                $flight->winter = $track->is_winter();

                $flight_type = new flight_type();
                $flight_type->do_retrieve(array('ftid', 'multi', 'multi_defined'), array('where_equals' => array('fn' => $this->type)));
                $flight->ftid = $flight_type->ftid;
                $flight->multi = ($this->defined ? $flight_type->multi_defined : $flight_type->multi);

                $flight->base_score = $track->{$this->type}->get_distance();
                $flight->coords = $track->{$this->type}->get_coordinates();
                $flight->score = $flight->base_score * $flight->multi;

                $flight->file = '/uploads/track/' . $flight->fid . '/track.igc';
                $flight->do_save();

                jquery::colorbox(array('html' => 'Your flight has been added successfully'));
                $form = new igc_form();
                ajax::update($form->get_html()->get());
            } else {
                jquery::colorbox(array('html' => 'Your flight has failed to save'));
            }
        }
    }

    public function do_validate() {
        if (!$this->agree) {
            $this->validation_errors['agree'] = 'You must agree to the terms to continue';
        }
        return parent::do_validate();
    }

}

?>



