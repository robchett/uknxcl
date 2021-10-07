<?php

namespace module\add_flight\form;

use classes\ajax;
use classes\attribute_callable;
use classes\attribute_list;
use classes\get;
use core;
use form\form;
use html\node;
use model\flight_type;
use track\defined_task;
use track\igc_parser;
use track\igcPartResult;
use track\igcResult;

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

    public string $coords;
    public string $file;

    public function __construct() {
        $fields = [
            new \form\field_file('kml',
                label: '',
                required: true,),
            new \form\field_string('coords',
                label: 'Defined flight coordinates',
                wrapper_class: ['callout', 'callout-tertiary', 'cf', 'coordinates'],
                required: false,
                pre_text: "<p>To submit a defined flight enter the coordinates below in 'XX000000;XX000000' format, with no ending \';\'<p>" . node::create('p i', [], "If you have declared your flight in your IGC file, they you don't need to do so again here."),
                post_text: node::create('p.defined_info'),                
            )
        ];
        parent::__construct($fields);
        $this->submit = 'Calculate';
        $this->pre_text = "<p>Upload a flight here to calculate scores, whilst the flight is being processed please feel free to complete tbe rest of the form below<p>";
        $this->post_text =
            node::create('div#kml_calc div#console a.calc', [], 'Calculate!') .
            node::create('div.callout.callout-secondary', [], "<p>Please note that depending on the flight this may take anywhere between 10 seconds and 5 mins. You can still use the other functions of the league while this calculates<p>
                <p>On submit the flight will be read by the system to make sure it conforms for the rules. It will then be displayed on the map.<p>",
            );
        $this->has_submit = false;
        $this->id = 'igc_upload_form';
        $this->h2 = 'Upload Form';
        $this->submittable = false;
    }

    public function do_validate(): bool {
        parent::do_validate();
        if (!isset($this->validation_errors['coords']) && !empty($this->coords) && !preg_match('/^((h[l-z]|n[a-hj-z]|s[a-hj-z]|t[abfglmqrvw])[0-9]{6};?){2,5}$/i', $this->coords)) {
            $this->validation_errors['coords'] = 'Coordinated are not valid';
        }
        if (!isset($this->validation_errors['kml']) && !isset($_FILES['kml']['tmp_name'])) {
            $this->validation_errors['kml'] = 'Your browser has not sent the correct data to the server. Please upgrade your browser!';
        }
        return count($this->validation_errors) == 0;
    }

    public function do_submit(): bool {
        $time = time();
        mkdir(root . '/.cache/' . $time);
        $file = root . '/.cache/' . $time . '/track.igc';
        if (isset($_FILES['kml'])) {
            move_uploaded_file((string) $_FILES['kml']['tmp_name'], $file);
        } else {
            copy($this->file, $file);
        }

        $this->do_calc_score($time);
        return true;
    }

    public function do_calc_score(int $id, ?int $section = null): void {
        $file = root . '/.cache/' . $id . '/track.igc';

        $igc_parser = new igc_parser();

        $data = [
            'source' => $file,
        ];

        if ($this->coords) {
            $task = defined_task::create_from_coordinates($this->coords);
            $data['task'] = [
                'type'        => 'os_gridref',
                'coordinates' => array_map(fn ($coordinate) => $coordinate['os_gridref'], $task->coordinates),
            ];
        }

        if ($section !== null) {
            $data['section'] = $section;
        }

        $parser = $igc_parser->exec($id, $data);

        $html = '';

        if ($parser === false) {
            $html .= node::create('p.callout.callout-warning', [], self::PARSE_FAILED);
        } else {
            if (!$this->check_date($parser)) {
                $html .= node::create('p.error', [], self::OUT_OF_BOUNDS_TEXT);
            }

            $validated = $parser->validated;
            if ($validated === false) {
                $html .= node::create('p.callout.callout-warning', [], self::NOT_VALID);
            } /*else if ($validated === null) {
                $html .= node::create('p.callout.callout-warning', [], self::NO_VALIDATION);
            }*/

            if ($parser instanceof igcPartResult) {
                $split = "1";
                $html .= $this->get_choose_track_html($parser);
            } else {
                $split = "0";
                $html .= $this->get_choose_score_html($parser);
            }
            ajax::add_script('map.add_flight(' . $id . ',1,1,1,' . $split . ');');
        }

        ajax::update('<div id="console">' . $html . '</div>');
    }

    public function check_date(igcResult|igcPartResult $parser): bool {
        $current_time = time();
        $closure_time = $current_time - (31 * 24 * 60 * 60);
        return (strtotime($parser->date) >= $closure_time && strtotime($parser->date) <= $current_time);
    }

    private function get_choose_track_html(igcPartResult $track): string {
        $parts = [];
        foreach ($track->sets as $key => $part) { 
            $parts[] = "<tr style='color:#" . get::kml_colour($key) . "'> 
                <td>Part: {$key}</td>
                <td>{$part->duration}s</td>
                <td>{$part->points}</td>
                <td><a class='choose button'" . new attribute_list(
                    dataAjaxClick: attribute_callable::create([$this, 'do_choose_track']),
                    dataAjaxPost: '{"track":' . $track->id . ', "section": ' . $key . '}',
                    dataAjaxShroud: '#igc_upload_form_wrapper',
                ) . ">Choose</a></td>
            </tr>";
        }
        return "
        <table>
            <thead>
                <tr>
                    <th>Part</th>
                    <th>Duration</th>
                    <th>Points</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                " . implode('', $parts) . "
            <tbody>
        </table>";
    }

    private function get_choose_score_html(igcResult $parser): string {
        return "
        <table>
            <thead>
                <tr>
                    <th>Type<th>
                    <th>Base Score / Multiplier<th>
                    <th>Score<th>
                </tr>
            </thead>
            <tbody>
                " . $this->get_task_select_html($parser, 'open_distance', flight_type::get_multiplier(flight_type::OD_ID)) . "
                " . $this->get_task_select_html($parser, 'out_and_return', flight_type::get_multiplier(flight_type::OR_ID)) . "
                " . $this->get_task_select_html($parser, 'triangle', flight_type::get_multiplier(flight_type::TR_ID)) . "
                " . $this->get_task_select_html($parser, 'flat_triangle', flight_type::get_multiplier(flight_type::FT_ID)) . "
                " . $this->get_defined_task_select_html($parser) . "
            </tbody>
        </table>";
    }

    private function get_task_select_html(igcResult $parser, string $type, float $multi): string {
        if ($task = $parser->get_task($type)) {
            return "
            <tr>
                <td>{$task->title}<td>
                <td>" . number_format($task->distance, 2) . " / " . number_format($multi, 2) . "</td>
                <td>" . number_format($task->distance * $multi, 2) . "</td>
                <td><a class='button score_select' data-post='{\"track\":{$parser->id},\"type\":\"{$task->type}\"}'>Choose</a></td>
            </tr>";
        }
        return '';
    }

    private function get_defined_task_select_html(igcResult $parser): string {
        if ($task = $parser->get_task('declared')) {
            $multiplier = flight_type::get_multiplier($task->type, (int) date('Y'), true);
            return "
            <tr>
                <td>{$task->title}<td>
                <td>" . number_format($task->distance, 3) . ' / ' . number_format($multiplier, 2) . "</td>
                <td>" . number_format($task->distance * $multiplier, 3) . "</td>
                " . ($parser->is_task_completed() ? "<td><a class='button score_select' data-post='{\"track\":{$parser->id},\"type\":\"task\"}'>Choose</a></td>" : node::create('td span.button.score_select', [], 'Not Valid')) . "
            </tr>";
        }
        return '';
    }

    public static function do_choose_track(): void {
        $class = new self();
        $class->do_calc_score((int) $_REQUEST['track'], (int)$_REQUEST['section']);
    }

    public static function reset(): void {
        $t = new self();
        ajax::update($t->get_html());
    }

    public function get_html(): string {
        $html = parent::get_html();
        $script = '';
        if (!isset($this->kml) || empty($this->kml)) {
            $script .= '$("#kml_calc").hide();';
        }
        if (!$this->coords) {
            $script .= '$("#add_flight_box .fieldset_1").hide();';
        }
        if (ajax) {
            ajax::add_script($script);
        } else {
            core::$inline_script[] = $script;
        }
        return $html;
    }


}
