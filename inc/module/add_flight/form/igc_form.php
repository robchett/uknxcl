<?php
namespace module\add_flight\form;

use classes\ajax;
use classes\jquery;
use form\form;
use html\node;
use object\flight;
use object\flight_type;
use track\igc_parser;
use track\track;

class igc_form extends form {

    public $delay;
    public $agree;
    public $defined;
    public $ridge;
    public $temp_id;
    public $type;
    public $force_delay;

    public function __construct() {
        parent::__construct([
                form::create('field_link', 'pid')
                    ->set_attr('label', 'Pilot:')
                    ->set_attr('required', true)
                    ->set_attr('default', 'Choose A Pilot')
                    ->set_attr('post_text', node::create('a', ['data-ajax-click' => '\\form\\add_pilot_form:get_form'], 'Not in the list? Click here to add a new pilot'))
                    ->set_attr('link_module', '\\object\\pilot')
                    ->set_attr('link_field', 'name')
                    ->set_attr('options', ['order' => 'name']),
                form::create('field_link', 'gid')
                    ->set_attr('label', 'Glider:')
                    ->set_attr('required', true)
                    ->set_attr('post_text', node::create('a', ['data-ajax-click' => '\\form\\add_glider_form:get_form'], 'Not in the list? Click here to add a new glider'))
                    ->set_attr('link_module', '\\object\\glider')
                    ->set_attr('link_field', ['manufacturer.title', 'name'])
                    ->set_attr('options', ['join' => ['manufacturer' => 'manufacturer.mid = glider.mid'], 'order' => 'manufacturer.title, glider.name']),
                form::create('field_link', 'cid')
                    ->set_attr('label', 'Club:')
                    ->set_attr('required', true)
                    ->set_attr('link_module', '\\object\\club')
                    ->set_attr('link_field', 'title')
                    ->set_attr('options', ['order' => 'title']),
                form::create('field_link', 'lid')
                    ->set_attr('label', 'Launch:')
                    ->set_attr('required', true)
                    ->set_attr('link_module', '\\object\\launch_type')
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
                form::create('field_int', 'temp_id')
                    ->set_attr('required', true)
                    ->set_attr('hidden', true),
                form::create('field_string', 'type')
                    ->set_attr('required', true)
                    ->set_attr('hidden', true),
            ]
        );

        $this->h2 = 'Additional Details';
        $this->id = 'igc_form';
        $this->name = 'igc';
        $this->title = 'Add Flight Form';

        $this->attributes['class'][] = 'form-compact';

        if (!ajax) {
            $this->submittable = false;
        }
    }

    public function do_submit() {
        $flight = new flight();
        $flight->set_from_request();
        $flight->live = false;
        $flight->do_save();

        if ($flight->fid) {
            $flight->move_temp_files($this->temp_id);

            $igc_parser = new igc_parser();
            $igc_parser->load_data($flight->fid, false);

            $flight->date = $igc_parser->get_date();
            $flight->did = $igc_parser->has_height_data() ? 3 : 2;
            $flight->winter = $igc_parser->is_winter();

            $flight->season = $igc_parser->get_date('Y');
            if($igc_parser->get_date('m') >= 11) {
                $flight->season++;
            }

            $this->force_delay = false;
            if (!$this->check_date($igc_parser)) {
                $this->force_delay = true;
                $flight->admin_info .= 'delayed as flight is old.';
            }
            $this->defined = ($this->type == 'task');

            $flight->duration = $igc_parser->get_duration();

            foreach (['od' => 'open_distance', 'or' => 'out_and_return', 'tr' => 'triangle'] as $task_id => $name) {
                if ($task = $igc_parser->get_task($name)) {
                    $flight->{$task_id . '_score'} = $task->get_distance();
                    $flight->{$task_id . '_time'} = $task->get_duration();
                    $flight->{$task_id . '_coordinates'} = $task->get_gridref();

                    if ($this->type == $task->type) {
                        $flight->ftid = $this->type;
                        $flight->base_score = $task->get_distance();
                        $flight->duration = $task->get_duration();
                        $flight->coords = $task->get_gridref();
                    }
                }
            }
            if ($this->defined) {
                $flight->ftid = $igc_parser->get_task('task')->type;
                $flight->base_score = $igc_parser->get_task('task')->get_distance();
                $flight->duration = $igc_parser->get_task('task')->get_duration();
                $flight->coords = $igc_parser->get_task('task')->get_gridref();
            }

            $flight->multi = (!$this->ridge ? flight_type::get_multiplier($flight->ftid, $igc_parser->get_date('Y'), $this->defined) : 1);
            $flight->score = $flight->multi * $flight->base_score;

            $flight->delayed = $this->force_delay || $this->delay;
            $flight->live = true;
            $flight->do_save();

            jquery::colorbox(['html' => 'Your flight has been added successfully', 'className'=> 'success']);
            $form = new igc_form();
            ajax::update($form->get_html()->get());
        } else {
            jquery::colorbox(['html' => 'Your flight has failed to save', 'className'=> 'success failure']);
        }
    }

    public function do_validate() {
        if (!$this->agree) {
            $this->validation_errors['agree'] = 'You must agree to the terms to continue';
        }
        return parent::do_validate();
    }

    public function check_date(igc_parser $parser) {
        $current_time = time();
        $closure_time = $current_time - (31 * 24 * 60 * 60);
        if (strtotime($parser->get_date()) >= $closure_time && strtotime($parser->get_date()) <= $current_time) {
            return true;
        } else {
            return false;
        }
    }
}