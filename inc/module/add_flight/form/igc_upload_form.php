<?php
namespace add_flight;

use form\form;
use html\node;

class igc_upload_form extends form {

    public function __construct() {
        $fields = array(
            form::create('field_file', 'kml')
                ->set_attr('label', '')
                ->set_attr('required', true)
                ->set_attr('external', true),
            form::create('field_string', 'coords')
                ->set_attr('label', 'Defined flight coordinates')
                ->set_attr('required_parent', 'defined')
                ->set_attr('required', false)
                ->set_attr('pre_text', node::create('p', [], 'To submit a defined flight enter the coordinates below in \'XX000000;XX000000\' format, with no ending \';\''))
                ->set_attr('post_text', node::create('p.defined_info'))
        );
        parent::__construct($fields);
        $this->submit = 'Calculate';
        $this->pre_text = node::create('p', [], 'Upload a flight here to calculate scores, whilst the flight is being processed please feel free to complete tbe rest of the form below');
        $this->post_text = node::create('p', [], 'Please note that depending on the flight this may take anywhere between 10 seconds and 5 mins. You can still use the other functions of the league while this calculates') . node::create('p', [], 'On submit the flight will be read by the system to make sure it conforms for the rules. It will then be displayed on the map.') . node::create('div#kml_calc div#console a.calc', [], 'Calculate!');
        $this->has_submit = false;
        $this->id = 'igc_upload_form';
        $this->h2 = 'Upload Form';
        $this->submittable = false;
    }

    public function do_choose_track() {
        $track = new \track();
        $track->id = $_REQUEST['track'];
        $this->create_track($track, $_REQUEST['start'], $_REQUEST['end']);
    }

    public function do_submit() {
        if (parent::do_submit()) {
            if (isset($_FILES['file'])) {
                $track = new \track();
                $track->id = time();
                $this->create_track($track);
            } else {
                \ajax::update(node::create('div#console', [], 'Your browser has not sent the correct data to the server. Please upgrade your browser!'));
            }
        }
    }

    private function create_track(\track $track, $start = 0, $end = 0) {
        $track->console("File upload accepted");
        $track->temp = true;
        $track->create_from_upload();
        $track->console("File moved and backed-up");

        $track->parse_IGC();
        if ($end || $start) {
            $track->truncate($start, $end);
        }
        $track->pre_calc();
        if (!$track->check_date()) {
            $track->error = node::create('p.error', [], 'Your flight is outside of the date range for submitting flights (31 days).<br/>
                    Please continue to enter the flight. It will not be visible until we clear it.<br/>
                    Please contact a member of the team with the reason why it has taken this long and if we see fit we will add it.'
            );
        }
        $defined = false;
        if (!empty($this->coords)) {
            $defined = $track->set_task($this->coords);
        }
        $track->console($track->get_number_of_parts() . ' Track' . ($track->get_number_of_parts() > 1 ? 's' : ''));
        $html = $track->error;
        if ($track->get_number_of_parts() > 1) {
            \geometry::time_split_kml_plus_js($track);
            $html .= $this->get_choose_track_html($track, $defined);
        } else {
            $track->calculate();
            $track->generate_output_files();
            $html .= $this->get_choose_score_html($track, $start, $end, $defined);
        }
        \ajax::update('<div id="console">' . $html . '</div>');
        \ajax::add_script('map.add_flight(' . $track->id . ',1,1,1);');
    }

    private function get_choose_track_html(\track $track) {
        $html = node::create('ul', [],
            $track->track_parts->iterate_return(
                function (\track_point_array $part, $i) use ($track) {
                    return node::create('li', [],
                        node::create('span', ['style' => 'color:#' . \get::colour($i - 1)], 'Track ' . $i . ' : ' . $part->get_time()) .
                        node::create('a.choose', ['data-ajax-click' => 'igc_upload_form:do_choose_track', 'data-ajax-post' => '\'{track:' . $track->id . ', start: ' . $part->start_point . ', end: ' . $part->end_point . '}\''], 'Choose')
                    );
                }
            )
        );
        return $html;
    }

    private function get_choose_score_html(\track $track, $start, $end, $defined) {
        $_SESSION['add_flight'][$track->id] = array('duration' => $track->get_duration(), 'start' => $start, 'end' => $end);
        $html = node::create('table', [],
            node::create('thead tr', [],
                node::create('th', [], 'Type') .
                node::create('th', [], 'Base Score / Multiplier') .
                node::create('th', [], 'Score')
            ) .
            node::create('tbody', [],
                $this->get_task_select_html($track, 'od') .
                $this->get_task_select_html($track, 'or') .
                $this->get_task_select_html($track, 'tr') .
                $this->get_task_select_html($track, 'ft') .
                ($defined ? $this->get_defined_task_select_html($track) : '')
            )
        );
        return $html;
    }

    private function get_defined_task_select_html(\track $track) {
        $_SESSION['add_flight'][$track->id]['task'] = [
            'type' => $track->task->type,
            'distance' => $track->task->get_distance(),
            'coords' => $track->task->get_coordinates(),
            'duration' => $track->task->get_duration()
        ];
        $multiplier = \flight_type::get_multiplier($track->task->ftid, date('Y'), true);
        return node::create('tr', [],
            node::create('td', [], $track->task->title) .
            node::create('td', [], $track->task->get_distance(3) . ' / ' . number_format($multiplier)) .
            node::create('td', [], $track->task->get_distance(3) * number_format($multiplier)) .
            node::create('td a.score_select choose', ['data-post' => '\'{"track":' . $track->id . ',"type":"task"}\''], 'Choose')
        );
    }

    private function get_task_select_html(\track $track, $type) {
        /** @var \task $task */
        $task = $track->$type;
        if (isset($task->waypoints)) {
            $_SESSION['add_flight'][$track->id][$type] = ['distance' => $task->get_distance(), 'coords' => $task->get_session_coordinates(), 'duration' => $task->get_duration()];
            $flight_type = new \flight_type();
            $flight_type->do_retrieve(['multi'], ['where' => 'fn=:fn', 'parameters' => ['fn' => $type]]);
            return node::create('tr', [],
                node::create('td', [], $task->title) .
                node::create('td', [], $task->get_distance(3) . ' / ' . number_format($flight_type->multi)) .
                node::create('td', [], $task->get_distance(3) * number_format($flight_type->multi)) .
                node::create('td a.score_select choose', ['data-post' => '\'{"track":' . $track->id . ',"type":"' . $type . '"}\''], 'Choose')
            );
        }
        return '';
    }

    public function do_validate() {
        parent::do_validate();
        if (!empty($this->coords) && !preg_match('/^((h[l-z]|n[a-hj-z]|s[a-hj-z]|t[abfglmqrvw])[0-9]{6};?){2,5}$/i', $this->coords)) {
            $this->validation_errors['coords'] = 'Coordinated are not valid';
        }
    }

    public function reset() {
        \ajax::update($this->get_html()->get());
    }

    public function get_html() {
        $html = parent::get_html();
        $script = '
        var dropZoneId = "kml_wrapper";
        var buttonId = "kml_wrapper";
        var mouseOverClass = "hover";
        var dropZone = $("#" + dropZoneId);
        var inputFile = dropZone.find("input");

        $(function () {
            if (document.getElementById(dropZoneId)) {
                document.getElementById(dropZoneId).addEventListener("dragover", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var ooleft = dropZone.offset().left;
                    var ooright = dropZone.outerWidth() + ooleft;
                    var ootop = dropZone.offset().top;
                    var oobottom = dropZone.outerHeight() + ootop;
                    dropZone.addClass(mouseOverClass);
                    var x = e.pageX;
                    var y = e.pageY;

                    if (!(x < ooleft || x > ooright || y < ootop || y > oobottom)) {
                        inputFile.offset({ top: y - 15, left: x - 100 });
                    } else {
                        inputFile.offset({ top: -400, left: -400 });
                    }
                }, true);
                if (buttonId != "") {
                    $("#" + buttonId).mousemove(function (e) {
                        var clickZone = $("#" + buttonId);
                        var oleft = clickZone.offset().left;
                        var oright = clickZone.outerWidth() + oleft;
                        var otop = clickZone.offset().top;
                        var obottom = clickZone.outerHeight() + otop;
                        var x = e.pageX;
                        var y = e.pageY;
                        if (!(x < oleft || x > oright || y < otop || y > obottom)) {
                            inputFile.offset({ top: y - 15, left: x - 160 });
                        } else {
                            inputFile.offset({ top: -400, left: -400 });
                        }
                    });
                }
                document.getElementById(dropZoneId).addEventListener("dragleave", function (e) {
                    dropZone.removeClass(mouseOverClass);
                });
                document.getElementById(dropZoneId).addEventListener("drop", function (e) {
                    $("#" + dropZoneId).removeClass(mouseOverClass);
                }, true);
            }
        });';
        if (!isset($this->kml) || empty($this->kml)) {
            $script .= '$("#kml_calc").hide();';
        }
        if (!isset($this->coords) || !$this->coords) {
            $script .= '$("#add_flight_box .fieldset_1").hide();';
        }
        if (ajax) {
            \ajax::add_script($script);
        } else {
            \core::$inline_script[] = $script;
        }
        return $html;
    }


}
