<?php

namespace module\add_flight\form;

use classes\ajax;
use classes\attribute_callable;
use classes\jquery;
use form\form;
use html\node;
use model\flight;
use model\flight_type;
use track\track;

class coordinates_form extends form {


    public $agree;
    public $coords;
    public $date;
    public $defined;
    public $delay;
    public bool $force_delay = false;
    public $ridge;
    private string $title;
    private string $name;

    public function __construct() {
        parent::__construct([
                form::create('field_link', 'pid')
                    ->set_attr('label', 'Pilot:')
                    ->set_attr('required', true)
                    ->set_attr('default', 'Choose A Pilot')
                    ->set_attr('post_text', node::create('a', ['data-ajax-click' => attribute_callable::create([\form\add_pilot_form::class, 'get_form'])], 'Not in the list? Click here to add a new pilot'))
                    ->set_attr('link_module', \model\pilot::class)
                    ->set_attr('link_field', 'name')
                    ->set_attr('options', ['order' => 'name']),
                form::create('field_link', 'gid')
                    ->set_attr('label', 'Glider:')
                    ->set_attr('required', true)
                    ->set_attr('post_text', node::create('a', ['data-ajax-click' => attribute_callable::create([\form\add_glider_form::class, 'get_form'])], 'Not in the list? Click here to add a new glider'))
                    ->set_attr('link_module', \model\glider::class)
                    ->set_attr('link_field', ['manufacturer.title', 'name'])
                    ->set_attr('options', ['join' => ['manufacturer' => 'manufacturer.mid = glider.mid'], 'order' => 'manufacturer.title, glider.name']),
                form::create('field_link', 'cid')
                    ->set_attr('label', 'Club:')
                    ->set_attr('required', true)
                    ->set_attr('link_module', \model\club::class)
                    ->set_attr('link_field', 'title')
                    ->set_attr('options', ['order' => 'title']),
                form::create('field_string', 'coords')
                    ->set_attr('label', 'Flight coordinates')
                    ->set_attr('required_parent', 'defined')
                    ->set_attr('required', true)
                    ->set_attr('pre_text', "<p>Enter the coordinates below in 'XX000000;XX000000' format, with no ending \';\'<p>")
                    ->add_wrapper_class('cf')
                    ->add_wrapper_class('coordinates')
                    ->set_attr('post_text', node::create('p.defined_info')),
                form::create('field_date', 'date')
                    ->set_attr('label', 'Date:')
                    ->set_attr('required', true),
                form::create('field_link', 'lid')
                    ->set_attr('label', 'Launch:')
                    ->set_attr('required', true)
                    ->set_attr('link_module', \model\launch_type::class)
                    ->set_attr('link_field', 'title'),
                form::create('field_boolean', 'ridge')
                    ->set_attr('label', 'The flight was predominantly in ridge lift, so according to the rules will not qualify for multipliers')
                    ->add_wrapper_class('long_text'),
                form::create('field_textarea', 'info')
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
            ]
        );

        $this->attributes['class'][] = 'form-compact';

        $this->h2 = 'Coordinate Flight';
        $this->id = 'coordinate_form';
        $this->name = 'coordinate';
        $this->title = 'Add Flight Form';
    }

    public function do_submit(): bool {
        $flight = new flight();
        $flight->set_from_request();
        $flight->dim = 1;

        if (strtotime($this->date) + (30 * 24 * 60 * 60) < time()) {
            $this->force_delay = true;
            $flight->admin_info .= 'delayed as flight is old.';
        }

        $track = new track();
        $task = $track->set_task($this->coords);
        $flight_type = new flight_type();
        $flight_type->do_retrieve(['ftid', 'multi', 'multi_defined'], ['where_equals' => ['fn' => $task->type]]);
        $flight->ftid = $flight_type->ftid;
        $flight->set_date($this->date);
        $flight->multi = !$this->ridge ? flight_type::get_multiplier($flight->ftid, $flight->season, $this->defined ?: false) : 1;
        $flight->base_score = $task->get_distance();
        $flight->coords = $this->coords;
        $flight->score = $flight->base_score * $flight->multi;
        $flight->delayed = $this->force_delay ? true : $this->delay;
        $flight->did = 1;
        $flight->do_save();

        jquery::colorbox(['html' => 'Your flight has been added successfully', 'className' => 'success']);
        $form = new coordinates_form();
        ajax::update((string)$form->get_html());
        return true;
    }

    public function do_validate(): bool {
        if (!$this->agree) {
            $this->validation_errors['agree'] = 'You must agree to the terms to continue';
        }
        if (!empty($this->coords) && !preg_match('/^((h[l-z]|n[a-hj-z]|s[a-hj-z]|t[abfglmqrvw])[0-9]{6};?){2,5}$/i', $this->coords)) {
            $this->validation_errors['coords'] = 'Coordinated are not valid';
        }
        return parent::do_validate();
    }

}





