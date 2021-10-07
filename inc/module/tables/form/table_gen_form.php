<?php

namespace module\tables\form;

use classes\ajax;
use classes\get;
use classes\tableOptions;
use core;
use form\form;
use html\node;
use JetBrains\PhpStorm\NoReturn;
use model\flight_type;
use model\launch_type;
use model\pilot;
use module\tables\model as _model;

class table_gen_form extends form
{

    public string $layout;
    public ?int $pilot;
    public string $title;
    public string $name;
    public string $post_submit_text;

    public function __construct()
    {
        $fields = [
            new \form\field_select('layout', label: 'Table Type:', default: '', options: [
                '0' => 'Standard',
                '1' => 'Club',
                '2' => 'Pilot',
                '3' => 'Top Tens',
                '4' => 'Plain List',
                '5' => 'Records',
            ],),
            new \form\field_link('pilot', label: 'Pilot:', link_module: pilot::class, link_field: 'name', options: new tableOptions(order: 'name ASC'), disabled: true, required: false,),
            new \form\field_string('year', label: 'Season:', default: '1991-' . date('Y',), required: false),
            new \form\field_select('gender', label: 'Gender:', options: [-1 => 'Both', 1 => 'Male', 2 => 'Female']),
            new \form\field_select('defined', label: 'Declared:', options: [-1 => 'Don\'t Filter', 1 => 'Yes', 0 => 'No'], default: '-1'),
            new \form\field_select('ridge', label: 'Ridge:', options: [-1 => 'Don\'t Filter', 1 => 'Yes', 0 => 'No'], default: '-1'),
            new \form\field_select('winter', label: 'Winter:', options: [-1 => 'Don\'t Filter', 1 => 'Winter Only', 0 => 'Summer Only'], default: '-1'),
            new \form\field_select('dimensions', label: 'KML Submitted:', options: [0 => 'Don\'t Filter', 3 => 'Yes (3D,', 2 => 'Yes (2D)', 1 => 'Yes (Both)', 4 => 'No']),
            new \form\field_select('glider_class', label: 'Glider Class:', options: [-1 => 'Don\'t Filter', 1 => 'Class 1', 5 => 'Class 5'], default: '-1'),
            new \form\field_select('Flights', label: '# of Flights', default: '', options: [6 => '6 Flights', 5 => '5 Flights', 4 => '4 Flights']),
            new \form\field_string('minimum_score', label: 'Minimum Distance', default: '10'),
            new \form\field_date('Date', label: 'Date', default: 0, required: false,),
            new \form\field_boolean('no_multipliers', label: 'No Multipliers'),
            new \form\field_boolean('show_top_4', label: 'Top Flights'),
            new \form\field_boolean('official', label: 'Official View'),
            new \form\field_boolean('split_classes', label: 'Split Classes'),
            new \form\field_string('flown_through', label: 'Flown through (eg. SU,TQ,', default: '', required: false,),
            new \form\field_boolean('handicap', label: 'Enable Handicapping'),
            new \form\field_string('handicap_kingpost', label: '-&gt; Kingpost', default: '1'),
            new \form\field_string('handicap_rigid', label: '-&gt; Class 5', default: '1'),
            new \form\field_checkboxes('launches', options: [launch_type::WINCH => 'Winch', launch_type::FOOT => 'Foot', launch_type::AERO => 'Aerotow'], label: 'Launch Type', default: [1,2,3],),
            new \form\field_checkboxes('types', options: [flight_type::OD_ID => 'Open Distance', flight_type::GO_ID => 'Goal', flight_type::OR_ID => 'Out and Return', flight_type::TR_ID => 'Triangle', flight_type::FT_ID => 'Flat Triangle'], label: 'Flight Type', default: [1,2,3,4,5]),
        ];

        parent::__construct($fields);

        $this->name = 'advTables';
        $this->title = 'Pre Calculation Checks';
        $this->description = '';
        $this->wrapper_class[] = 'advanced_tables_wrapper';
        $this->id = 'advanced_tables';
        $this->submit = 'Generate';
        //$this->h2 = 'Advanced Options';
    }

    public function get_html(): string
    {
        core::$inline_script[] = '$("#' . $this->id . ' #layout").change(function() {
            if($(this).val() == 2) {
                $("#' . $this->id . ' #pilot").attr("disabled", false);
            } else {
                $("#' . $this->id . ' #pilot").attr("disabled", true);
            }
        });';
        return parent::get_html();
    }

    public function set_from_options(_model\league_table $options): void
    {
        /** @psalm-suppress MixedAssignment */
        foreach ((array) $options as $key => $value) {
            if ($key == 'flown_through') {
                /** @psalm-suppress MixedArgument */
                $this->$key = implode(',', $value);
            } else if ($this->has_field((string) $key)) {
                $this->$key = $value;
            }
        }
        $this->pilot = $options->pilot_id;
        if ($this->layout == 2) {
            $this->get_field_from_name('pilot')->disabled = false;
        }
    }

    public function do_submit(): bool
    {
        $table = new _model\league_table();
        $table->set_from_request();
        ajax::update($table->get_table());
        return true;
    }

    public function get_submit(): string
    {
        if (!$this->has_submit) {
            return '';
        }
        if (!$this->submittable) {
            $this->submit_attributes->disabled = 'disabled';
        }
        $field = node::create(
            'div.form-group.submit-group div.col-md-offset-' . $this->bootstrap[0] . '.col-md-' . $this->bootstrap[1],
            [],
            node::create('button.btn.btn-default', $this->submit_attributes, $this->submit) . node::create('a.form_toggle', ['dataShow' => 'basic_tables_wrapper'], 'Basic View')
        );
        return $field;
    }
}
