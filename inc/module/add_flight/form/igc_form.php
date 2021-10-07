<?php

namespace module\add_flight\form;

use classes\ajax;
use classes\attribute_callable;
use classes\attribute_list;
use classes\email;
use classes\jquery;
use classes\tableOptions;
use Exception;
use form\add_glider_form;
use form\add_pilot_form;
use form\form;
use html\node;
use model\club;
use model\flight;
use model\flight_type;
use model\glider;
use model\launch_type;
use model\new_flight_notification;
use model\pilot;
use track\igc_parser;
use track\igcPartResult;
use track\igcResult;
use track\task;

class igc_form extends form
{

    public bool $delay;
    public bool $personal;
    public bool $agree;
    public bool $defined;
    public bool $ridge;
    public int $temp_id;
    public string $type;
    public bool $force_delay;
    public string $title;
    public string $name;
    public int $cid;
    public int $gid;
    public int $pid;
    public int $lid;
    public string $vis_info;
    public string $admin_info;

    public function __construct()
    {
        parent::__construct(
            [
                new \form\field_link(
                    'pid',
                    label: 'Pilot:',
                    required: true,
                    defaultText: 'Choose A Pilot',
                    post_text: node::create('a', ['dataAjaxClick' => attribute_callable::create([add_pilot_form::class, 'get_form'])], 'Not in the list? Click here to add a new pilot'),
                    link_module: pilot::class,
                    link_field: 'name',
                    options: new tableOptions(order: 'name')
                ),
                new \form\field_link(
                    'gid',
                    label: 'Glider:',
                    required: true,
                    post_text: node::create('a', ['dataAjaxClick' => attribute_callable::create([add_glider_form::class, 'get_form'])], 'Not in the list? Click here to add a new glider'),
                    link_module: glider::class,
                    link_field: ['manufacturer.title', 'name'],
                    options: new tableOptions(join: ['manufacturer' => 'manufacturer.mid = glider.mid'], order: 'manufacturer.title, glider.name')
                ),
                new \form\field_link(
                    'cid',
                    label: 'Club:',
                    required: true,
                    link_module: club::class,
                    link_field: 'title',
                    options: new tableOptions(order: 'title'),
                ),
                new \form\field_link(
                    'lid',
                    label: 'Launch:',
                    required: true,
                    link_module: launch_type::class,
                    link_field: 'title',
                ),
                new \form\field_boolean(
                    'ridge',
                    label: 'The flight was predominantly in ridge lift, so according to the rules will not qualify for multipliers',
                    wrapper_class: ['long_text'],
                ),
                new \form\field_textarea(
                    'vis_info',
                    label: 'Please write any extra information you wish to be made public here',
                    required: false,
                    wrapper_class: ['long_text'],
                ),
                new \form\field_textarea(
                    'admin_info',
                    label: 'Please write any extra information you wish to be seen by the admin team here',
                    required: false,
                    wrapper_class: ['long_text'],
                ),
                new \form\field_boolean(
                    'delay',
                    label: 'Publication of the flight should be delayed until it has been inspected by the admin team.',
                ),
                new \form\field_boolean(
                    'personal',
                    label: 'Show the flight in your personal log only / the flight was flown outside of the UK',
                ),
                new \form\field_boolean(
                    'agree',
                    label: 'The NXCL is free to publish the flight to the public and to be passed on to skywings for publication. The flight has not broken any airspace laws',
                    required: true,
                ),
                new \form\field_int(
                    'temp_id',
                    required: true,
                    hidden: true,
                ),
                new \form\field_string(
                    'type',
                    required: true,
                    hidden: true,
                ),
            ]
        );

        $this->h2 = 'Additional Details';
        $this->id = 'igc_form';
        $this->name = 'igc';
        $this->title = 'Add Flight Form';

        $this->attributes->class[] = 'form-compact';
    }

    public function do_submit(): bool
    {
        $igc_parser = igc_parser::load_data($this->temp_id);
        if ($igc_parser instanceof igcPartResult || !$igc_parser) {
            throw new Exception('Track invalid');
        }

        $date = strtotime($igc_parser->get_date());
        $season = (int) date('Y', $date);
        if (date('m', $date) >= 11) {
            $season++;
        }

        if (!$this->check_date($igc_parser)) {
            $this->delay = true;
            $this->admin_info .= "delayed as flight is old.\n";
        }

        if ($igc_parser->validated === false) {
            $this->admin_info .= "G record invalid.\n";
        }

        $tracks = [
            'ftid' => 0,
            'base_score' => 0,
            'duration' => 0,
            'coords' => 0
        ];
        foreach (['od' => 'open_distance', 'or' => 'out_and_return', 'tr' => 'triangle', 'ft' => 'flat_triangle'] as $task_id => $name) {
            if ($task = $igc_parser->get_task($name)) {
                $tracks[$task_id . '_score'] = $task->get_distance();
                $tracks[$task_id . '_time'] = $task->get_duration();
                $tracks[$task_id . '_coordinates'] = $task->get_gridref();

                if ($this->type == $task->type) {
                    $tracks['ftid'] = $this->type;
                    $tracks['base_score'] = $task->get_distance();
                    $tracks['duration'] = $task->get_duration();
                    $tracks['coords'] = $task->get_gridref();
                }
            }
        }
        if ($this->type == 'task' && ($task = $igc_parser->get_task('declared'))) {
            $tracks['ftid'] = $task->type;
            if ($tracks['ftid'] == task::TYPE_OPEN_DISTANCE) {
                $tracks['ftid'] = task::TYPE_GOAL;
            }
            $tracks['base_score'] = $task->get_distance();
            $tracks['duration'] = $task->get_duration();
            $tracks['coords'] = $task->get_gridref();
        }

        $tracks['multi'] = (!$this->ridge ? flight_type::get_multiplier($tracks['ftid'], (int) $igc_parser->get_date('Y'), $this->type == 'task') : 1);
        $tracks['score'] = $tracks['multi'] * $tracks['base_score'];

        $fid = flight::do_save([
            'pid' => $this->pid,
            'gid' => $this->gid,
            'cid' => $this->cid,
            'lid' => $this->lid,
            'ridge' => $this->ridge,
            'vis_info' => $this->vis_info,
            'admin_info' => $this->admin_info,
            'delayed' => $this->delay,
            'personal' => $this->personal,
            'did' => $igc_parser->has_height_data() ? 3 : 2,
            'winter' => $igc_parser->is_winter(),
            'date' => $date,
            'season' => $season,
            'defined' => $this->type == 'task',
            'duration' => $igc_parser->get_duration(),
        ] + $tracks);

        if ($fid) {
            flight::move_temp_files($fid, $this->temp_id);
            jquery::colorbox(['html' => 'Your flight has been added successfully', 'className' => 'success']);
            $form = new igc_form();
            ajax::update($form->get_html());
            $users = new_flight_notification::get_all(new tableOptions());
            foreach ($users as $user) {
                $mail = new email();
                $mail->load_template(root . '/template/email/basic.html');
                $mail->set_recipients([$user->email]);
                $subject = 'New flight added';
                if ($this->delay) {
                    $subject .= ' - Delayed';
                }
                if ($igc_parser->validated === false) {
                    $subject .= ' - G record invalid';
                }
                $mail->set_subject($subject);
                $mail->replacements = [
                    '[content]' => '
                        <h2>New flight added: ' . $fid . '</h2>
                        <!--suppress HtmlDeprecatedAttribute -->
                        <table class="btn-primary" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <a href="' . host . '/cms/edit/2/' . $fid . '">View in CMS</a>
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

    public function check_date(igcResult $parser): bool
    {
        $current_time = time();
        $closure_time = $current_time - (31 * 24 * 60 * 60);
        if (strtotime($parser->get_date()) >= $closure_time && strtotime($parser->get_date()) <= $current_time) {
            return true;
        } else {
            return false;
        }
    }

    public function do_validate(): bool
    {
        if (!$this->agree) {
            $this->validation_errors['agree'] = 'You must agree to the terms to continue';
        }
        return parent::do_validate();
    }
}
