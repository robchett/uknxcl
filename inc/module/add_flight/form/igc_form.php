<?php

namespace module\add_flight\form;

use classes\ajax;
use classes\attribute_callable;
use classes\attribute_list;
use classes\email;
use classes\jquery;
use form\form;
use html\node;
use model\flight;
use model\flight_type;
use model\new_flight_notification;
use track\igc_parser;
use track\task;

class igc_form extends form {

    public $delay;
    public $agree;
    public $defined;
    public $ridge;
    public $temp_id;
    public $type;
    public $force_delay;
    public string $title;
    public string $name;
    public $cid;
    public $gid;
    public $pid;
    public string $vis_info;

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
                form::create('field_link', 'lid')
                    ->set_attr('label', 'Launch:')
                    ->set_attr('required', true)
                    ->set_attr('link_module', \model\launch_type::class)
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

    public function do_submit(): bool {
        $flight = new flight();
        $flight->set_from_request();
        $flight->live = false;
        $flight->do_save();

        if ($flight->fid) {
            $flight->move_temp_files($this->temp_id);

            $igc_parser = new igc_parser();
            $igc_parser->load_data($flight->fid, false);

            $flight->did = $igc_parser->has_height_data() ? 3 : 2;
            $flight->winter = $igc_parser->is_winter();
            $flight->set_date(strtotime($igc_parser->get_date()));

            $this->force_delay = false;
            if (!$this->check_date($igc_parser)) {
                $this->force_delay = true;
                $flight->admin_info .= "delayed as flight is old.\n";
            }

            if ($igc_parser->get_validated() === 0) {
                $flight->admin_info .= "G record invalid.\n";
            }
            $flight->defined = ($this->type == 'task');

            $flight->duration = $igc_parser->get_duration();

            foreach (['od' => 'open_distance', 'or' => 'out_and_return', 'tr' => 'triangle', 'ft' => 'flat_triangle'] as $task_id => $name) {
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
            if ($flight->defined) {
                $task = $igc_parser->get_task('declared');
                $flight->ftid = $task->type;
                if ($flight->ftid == task::TYPE_OPEN_DISTANCE) {
                    $flight->ftid = task::TYPE_GOAL;
                }
                $flight->base_score = $task->get_distance();
                $flight->duration = $task->get_duration();
                $flight->coords = $task->get_gridref();
            }

            $flight->multi = (!$flight->ridge ? flight_type::get_multiplier($flight->ftid, $igc_parser->get_date('Y'), $flight->defined) : 1);
            $flight->score = $flight->multi * $flight->base_score;

            $flight->delayed = $this->force_delay || $this->delay;
            $flight->live = true;
            $flight->do_save();

            jquery::colorbox(['html' => 'Your flight has been added successfully', 'className' => 'success']);
            $form = new igc_form();
            ajax::update((string)$form->get_html());
            $users = new_flight_notification::get_all([]);
            foreach ($users as $user) {
                $mail = new email();
                $mail->load_template(root . '/template/email/basic.html');
                $mail->set_recipients([$user->email]);
                $subject = 'New flight added';
                if ($flight->delayed) {
                    $subject .= ' - Delayed';
                }
                if ($igc_parser->get_validated() === 0) {
                    $subject .= ' - G record invalid';
                }
                $mail->set_subject($subject);
                $mail->replacements = [
                    '[content]' => '
                        <h2>New flight added: ' . $flight->get_primary_key() . '</h2>
                        <!--suppress HtmlDeprecatedAttribute -->
                        <table class="btn-primary" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <a href="' . host . '/cms/edit/2/' . $flight->get_primary_key() . '">View in CMS</a>
                                </td>
                            </tr>
                        </table>',
                ];
                $mail->send();
            }
        } else {
            jquery::colorbox(['html' => 'Your flight has failed to save', 'className' => 'success failure']);
        }
        return true;
    }

    public function check_date(igc_parser $parser): bool {
        $current_time = time();
        $closure_time = $current_time - (31 * 24 * 60 * 60);
        if (strtotime($parser->get_date()) >= $closure_time && strtotime($parser->get_date()) <= $current_time) {
            return true;
        } else {
            return false;
        }
    }

    public function do_validate(): bool {
        if (!$this->agree) {
            $this->validation_errors['agree'] = 'You must agree to the terms to continue';
        }
        return parent::do_validate();
    }
}