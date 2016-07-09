<?php
namespace module\add_flight\form;

use classes\ajax;
use classes\get;
use form\form;
use html\node;
use object\flight_type;
use track\defined_task;
use track\igc_parser;

class igc_upload_form extends form {

    const OUT_OF_BOUNDS_TEXT = '
        Your flight is outside of the date range for submitting flights (31 days).<br/>
        Please continue to enter the flight. It will not be visible until we clear it.<br/>
        Please contact a member of the team with the reason why it has taken this long and if we see fit we will add it.';

    const NO_VALIDATION = '
        Your flight did not contain a valid G record<br/>
        While this doen\'t currently effect flights, the record is required to make sure the IGC hasn\'t been tampered with.';

    const NOT_VALID = '
        Your flight\'s G record was not valid<br/>
        Usually this is because it is missing entierly (GPSDump doesn\'t provide it).<br/>
        You can continue to submit the flight and an admin will investigate the track<br/>
        Please provide any details if you have amended the track.';

    const PARSE_FAILED = '
        We were unable to parse the flight. Please check the IGC<br/>
        If you think the problem is us (likely) please let us know and we\'ll investigate';

    public $coords;

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
                ->set_attr('pre_text', node::create('p', [], 'To submit a defined flight enter the coordinates below in \'XX000000;XX000000\' format, with no ending \';\'') . node::create('p i', [], "If you have declared your flight in your IGC file, they you don't need to do so again here."))
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

    public function do_validate() {
        parent::do_validate();
        if (!isset($this->validation_errors['coords']) &&!empty($this->coords) && !preg_match('/^((h[l-z]|n[a-hj-z]|s[a-hj-z]|t[abfglmqrvw])[0-9]{6};?){2,5}$/i', $this->coords)) {
            $this->validation_errors['coords'] = 'Coordinated are not valid';
        }
        if (!isset($this->validation_errors['kml']) && !isset($_FILES['kml']['tmp_name'])) {
            $this->validation_errors['kml'] = 'Your browser has not sent the correct data to the server. Please upgrade your browser!';
        }
        return count($this->validation_errors) == 0;
    }

    public function do_calc_score($id, $section = null) {
        $file = root . '/.cache/' . $id . '/track.igc';

        $igc_parser = new igc_parser();

        $data = [
            'source' => $file
        ];

        if ($this->coords) {
            $task = new defined_task();
            $task->create_from_coordinates($this->coords);
            $data['task'] = [
                'type' => 'os_gridref',
                'coordinates' => array_map(function($coordinate) {return $coordinate->os_gridref;}, $task->coordinates)
            ];
        }

        if ($section !== null) {
            $data['section'] = $section;
        }

        $parsed = $igc_parser->exec($id, $data);

        $html = '';

        if ($parsed) {
            if (!$this->check_date($igc_parser)) {
                $html .= node::create('p.error', [], static::OUT_OF_BOUNDS_TEXT);
            }

            $validated = $igc_parser->get_validated();
            if ($validated === 0) {
                $html .= node::create('p.callout.callout-warning', [], static::NOT_VALID);
            } /*else if ($validated === null) {
                $html .= node::create('p.callout.callout-warning', [], static::NO_VALIDATION);
            }*/

            if ($igc_parser->get_part_count() > 1) {
                $split = "1";
                $html .= $this->get_choose_track_html($igc_parser);
            } else {
                $split = "0";
                $html .= $this->get_choose_score_html($igc_parser);
            }
            ajax::add_script('map.add_flight(' . $id . ',1,1,1,' . $split . ');');
        } else {
            $html .= node::create('p.callout.callout-warning', [], static::PARSE_FAILED);
        }

        ajax::update('<div id="console">' . $html . '</div>');
    }

    public function do_submit() {
        $time = time();
        mkdir(root . '/.cache/' . $time);
        $file = root . '/.cache/' . $time . '/track.igc';
        if (isset($_FILES['kml'])) {
            move_uploaded_file($_FILES['kml']['tmp_name'], $file);
        } else {
            copy($this->file, $file);
        }

        $this->do_calc_score($time);
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

    public function do_choose_track() {
        $this->do_calc_score($_REQUEST['track'], (int) $_REQUEST['section']);
    }

    private function get_choose_track_html(igc_parser $track) {
        $parts = [];
        foreach ($track->get_split_parts() as $key => $part) {
            $parts[] = node::create('tr',  ['style' => 'color:#' . get::kml_colour($key)], [
                node::create('td', [], 'Part: ' . $key),
                node::create('td', [], $part->duration . 's'),
                node::create('td', [], $part->points),
                node::create('td a.choose.button', [
                    'data-ajax-click' => get_class($this) . ':do_choose_track',
                    'data-ajax-post'  => '{"track":' . $track->id . ', "section": ' . $key . '}',
                    'data-ajax-shroud'  => '#igc_upload_form_wrapper',
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

    private function get_choose_score_html(igc_parser $parser) {
        $html = node::create('table', [], [
            node::create('thead tr', [], [
                node::create('th', [], 'Type'),
                node::create('th', [], 'Base Score / Multiplier'),
                node::create('th', [], 'Score')
            ]),
            node::create('tbody', [], [
                $this->get_task_select_html($parser, 'open_distance', flight_type::get_multiplier(flight_type::OD_ID)),
                $this->get_task_select_html($parser, 'out_and_return', flight_type::get_multiplier(flight_type::OR_ID)),
                $this->get_task_select_html($parser, 'triangle', flight_type::get_multiplier(flight_type::TR_ID)),
                $this->get_task_select_html($parser, 'flat_triangle', flight_type::get_multiplier(flight_type::FT_ID)),
                $this->get_defined_task_select_html($parser)
            ])
        ]);
        return $html;
    }

    private function get_defined_task_select_html(igc_parser $parser) {
        if ($task = $parser->get_task('declared')) {
            $multiplier = flight_type::get_multiplier($task->type, date('Y'), true);
            return node::create('tr', [], [
                node::create('td', [], $task->title),
                node::create('td', [], number_format($task->distance, 3) . ' / ' . number_format($multiplier, 2)),
                node::create('td', [], number_format($task->distance * $multiplier, 3)),
                $parser->is_task_completed() ?
                    node::create('td a.button.score_select', ['data-post' => '{"track":' . $parser->id . ',"type":"task"}'], 'Choose') :
                    node::create('td span.button.score_select', [], 'Not Valid')
            ]);
        }
        return '';
    }

    private function get_task_select_html(igc_parser $parser, $type, $multi) {
        if ($task = $parser->get_task($type)) {
            return node::create('tr', [], [
                node::create('td', [], $task->title),
                node::create('td', [], number_format($task->distance, 2) . ' / ' . number_format($multi, 2)),
                node::create('td', [], number_format($task->distance * $multi, 2)),
                node::create('td a.button.score_select', ['data-post' => '{"track":' . $parser->id . ',"type":"' . $task->type . '"}'], 'Choose')
            ]);
        }
        return '';
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
