<?php
namespace module\add_flight\form;

use classes\ajax;
use classes\compiler;
use classes\geometry;
use classes\get;
use classes\session;
use form\form;
use html\node;
use object\flight_type;
use track\defined_task;
use track\track;
use track\track_part;

class igc_upload_form extends form {

    /** @var \track\defined_task */
    public $task;

    public function __construct() {
        $fields = [
            form::create('field_file', 'kml')
                ->set_attr('label', '')
                ->set_attr('required', true)
                ->set_attr('external', true),
            form::create('field_string', 'coords')
                ->set_attr('label', 'Defined flight coordinates')
                ->set_attr('required_parent', 'defined')
                ->set_attr('wrapper_class', ['callout', 'callout-tertiary', 'cf'])
                ->set_attr('required', false)
                ->set_attr('pre_text', node::create('p', [], 'To submit a defined flight enter the coordinates below in \'XX000000;XX000000\' format, with no ending \';\''))
                ->set_attr('post_text', node::create('p.defined_info'))
        ];
        parent::__construct($fields);
        $this->submit = 'Calculate';
        $this->pre_text = node::create('p', [], 'Upload a flight here to calculate scores, whilst the flight is being processed please feel free to complete tbe rest of the form below');
        $this->post_text =
            node::create('div#kml_calc div#console a.calc', [], 'Calculate!') .
            node::create('div.callout.callout-secondary', [], [
                node::create('p', [], 'Please note that depending on the flight this may take anywhere between 10 seconds and 5 mins. You can still use the other functions of the league while this calculates'),
                node::create('p', [], 'On submit the flight will be read by the system to make sure it conforms for the rules. It will then be displayed on the map.')
            ]);
        $this->has_submit = false;
        $this->id = 'igc_upload_form';
        $this->h2 = 'Upload Form';
        $this->submittable = false;
    }

    public function do_choose_track() {
        $track = new track($_REQUEST['track']);
        $this->create_track($track, $_REQUEST['section']);
    }

    public function do_submit() {
        compiler::disable();
        if (isset($_FILES['kml'])) {
            $track = new track();
            $this->create_track($track);
        } else {
            ajax::update(node::create('div#console', [], 'Your browser has not sent the correct data to the server. Please upgrade your browser!'));
        }
    }

    private function create_track(track $track, $section = null) {
        $track->temp = true;
        $track->create_from_upload();
        $track->parse_IGC();
        if($section) {
            $track->set_section($section);
        }
        if (!$track->check_date()) {
            $track->error = node::create('p.error', [], 'Your flight is outside of the date range for submitting flights (31 days).<br/>
                    Please continue to enter the flight. It will not be visible until we clear it.<br/>
                    Please contact a member of the team with the reason why it has taken this long and if we see fit we will add it.'
            );
        }
        $defined = false;
        if (!empty($this->coords)) {
            $this->task = new defined_task($this->coords);
            $defined = $this->task->is_valid($track);
        }
        $html = $track->error;
        if ($track->get_number_of_parts() > 1) {
            $track->generate_split_output_files();
            $html .= $this->get_choose_track_html($track, $defined);
        } else {
            $track->calculate();
            $track->generate_output_files();
            $html .= $this->get_choose_score_html($track, $section, $defined);
        }
        ajax::update('<div id="console">' . $html . '</div>');
        ajax::add_script('map.add_flight(' . $track->id . ',1,1,1);');
    }

    private function get_choose_track_html(track $track) {
        $parts = [];
        for ($i = 0; $i < $track->get_number_of_parts(); $i++) {
            $parts[] = node::create('tr',  ['style' => 'color:#' . get::kml_colour($i)], [
                node::create('td', [], 'Part: ' . $i),
                node::create('td', [], $track->get_part_duration($i) . 's'),
                node::create('td', [], $track->get_part_length($i)),
                node::create('td a.choose.button', [
                    'data-ajax-click' => get_class($this) . ':do_choose_track',
                    'data-ajax-post'  => '{"track":' . $track->id . ', "section": ' . $i . '}'
                ], 'Choose')
            ]);
        }
        $html = node::create('table', [], [
            node::create('thead tr', [], [
                node::create('th', [], 'Part'),
                node::create('th', [], 'Duration'),
                node::create('th', [], 'Points'),
                node::create('th', [], '')
            ]),
            node::create('tbody', [], $parts)
        ]);
        return $html;
    }

    private function get_choose_score_html(track $track, $section, $defined) {
        session::set([
            'duration' => $track->get_duration(),
            'section'  => $section
        ], 'add_flight', $track->id);
        $html = node::create('table', [], [
            node::create('thead tr', [], [
                node::create('th', [], 'Type'),
                node::create('th', [], 'Base Score / Multiplier'),
                node::create('th', [], 'Score')
            ]),
            node::create('tbody', [], [
                $this->get_task_select_html($track, 'od'),
                $this->get_task_select_html($track, 'or'),
                $this->get_task_select_html($track, 'tr'),
                //$this->get_task_select_html($track, 'ft'),
                ($defined ? $this->get_defined_task_select_html($track) : '')
            ])
        ]);
        return $html;
    }

    private function get_defined_task_select_html(track $track) {
        session::set([
                'type'     => $this->task->type,
                'distance' => $this->task->get_distance(),
                'coords'   => $this->task->get_coordinates(),
                'duration' => $this->task->get_duration()
            ], 'add_flight', $track->id, 'task'
        );
        $multiplier = flight_type::get_multiplier($this->task->ftid, date('Y'), true);
        return node::create('tr', [], [
            node::create('td', [], $this->task->title),
            node::create('td', [], number_format($this->task->get_distance(), 3) . ' / ' . number_format($multiplier, 2)),
            node::create('td', [], number_format($this->task->get_distance() * $multiplier, 3)),
            node::create('td a.button.score_select', ['data-post' => '{"track":' . $track->id . ',"type":"task"}'], 'Choose')
        ]);
    }

    private function get_task_select_html(track $track, $type) {
        /** @var \task $task */
        $task = $track->$type;
        if ($task->get_distance()) {
            session::set([
                    'distance' => $task->get_distance(),
                    'coords'   => $task->get_gridref(),
                    'duration' => $task->get_duration()
                ], 'add_flight', $track->id, $type
            );
            $flight_type = new flight_type();
            $flight_type->do_retrieve(['multi', 'title'], ['where' => 'fn=:fn', 'parameters' => ['fn' => $type]]);
            return node::create('tr', [], [
                node::create('td', [], $flight_type->title),
                node::create('td', [], number_format($task->get_distance(), 2) . ' / ' . number_format($flight_type->multi, 2)),
                node::create('td', [], number_format($task->get_distance() * $flight_type->multi, 2)),
                node::create('td a.button.score_select', ['data-post' => '{"track":' . $track->id . ',"type":"' . $type . '"}'], 'Choose')
            ]);
        }
        return '';
    }

    public function do_validate() {
        parent::do_validate();
        if (!empty($this->coords) && !preg_match('/^((h[l-z]|n[a-hj-z]|s[a-hj-z]|t[abfglmqrvw])[0-9]{6};?){2,5}$/i', $this->coords)) {
            $this->validation_errors['coords'] = 'Coordinated are not valid';
        }
        return !count($this->validation_errors);
    }

    public function reset() {
        ajax::update($this->get_html()->get());
    }

    public function get_html() {
        $html = parent::get_html();
        $script = '';
        if (!isset($this->kml) || empty($this->kml)) {
            $script .= '$("#kml_calc").hide();';
        }
        if (!isset($this->coords) || !$this->coords) {
            $script .= '$("#add_flight_box .fieldset_1").hide();';
        }
        if (ajax) {
            ajax::add_script($script);
        } else {
            \core::$inline_script[] = $script;
        }
        return $html;
    }


}
