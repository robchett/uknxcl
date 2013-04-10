<?php

class igc_form extends form {


    public function __construct() {
        parent::__construct(array(
                form::create('field_select', 'pid')
                    ->set_attr('label', 'Pilot:')
                    ->set_attr('required', true)
                    ->set_attr('default', 'Choose A Pilot')
                    ->set_attr('options', alphabeticalise::pilot_array())
                    ->set_attr('post_text', '<a data-ajax-click="add_pilot_form:get_form">Not in the list? Click here to add a new pilot</a>'),
                form::create('field_select', 'gid')
                    ->set_attr('label', 'Glider:')
                    ->set_attr('required', 1)
                    ->set_attr('options', alphabeticalise::glider_array())
                    ->set_attr('post_text', '<a data-ajax-click="add_glider_form:get_form">Not in the list? Click here to add a new glider</a>'),
                form::create('field_select', 'cid')
                    ->set_attr('label', 'Club:')
                    ->set_attr('required', 1)
                    ->set_attr('options', alphabeticalise::club_array()),
                form::create('field_radio', 'type')
                    ->set_attr('name', 'type'),
                form::create('field_select', 'launch')
                    ->set_attr('label', 'Launch:')
                    ->set_attr('required', 1)
                    ->set_attr('options', flight::$launch_types),
                form::create('field_boolean', 'ridge')
                    ->set_attr('label', 'The flight was predominantly in ridge lift, so according to the rules will not qualify for multipliers')
                    ->set_attr('value', 'Yes')
                    ->add_wrapper_class('long_text'),
                form::create('field_textarea', 'vis_info')
                    ->set_attr('label', 'Please write any extra information you wish to be made public here')
                    ->add_wrapper_class('long_text'),
                form::create('field_textarea', 'invis_info')
                    ->set_attr('label', 'Please write any extra information you wish to be seen by the admin team here')
                    ->add_wrapper_class('long_text'),
                form::create('field_boolean', 'delay')
                    ->set_attr('label', 'Publication of the flight should be delayed until it has been inspected by the admin team.')
                    ->set_attr('value', 'Yes')
                    ->add_wrapper_class('long_text'),
                form::create('field_boolean', 'personal')
                    ->set_attr('label', 'Show the flight in your personal log only / the flight was flown outside of the UK')
                    ->set_attr('value', 'Yes')
                    ->add_wrapper_class('long_text'),
                form::create('field_boolean', 'agree')
                    ->set_attr('label', 'The NXCL is free to publish the flight to the public and to be passed on to skywings for publication. The flight has not broken any airspace laws')
                    ->set_attr('value', 'Yes')
                    ->set_attr('required', true)
                    ->add_wrapper_class('long_text'),
            )
        );

        $this->h2 = 'Additional Detials';
        $this->id = 'igc_form';
        $this->name = 'igc';
        $this->title = 'Add Flight Form';
        $this->submittable = false;
    }


    public function do_submit() {

    }

}

?>



