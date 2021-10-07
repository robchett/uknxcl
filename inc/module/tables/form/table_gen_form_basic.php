<?php

namespace module\tables\form;

use classes\ajax;
use classes\get;
use classes\tableOptions;
use core;
use form\field;
use form\form;
use html\node;
use JetBrains\PhpStorm\NoReturn;
use model\pilot;
use module\tables\model as _model;
use module\tables\model\league_table;

class table_gen_form_basic extends form
{

    public bool $glider_mode;
    public bool $no_min;
    public int $pilot;
    public bool $split_classes;
    public string $type;
    public string $year;
    public string $shroud;
    public string $post_submit_text;

    public function __construct()
    {
        $years = ['all_time' => 'All Time'];
        foreach (range(date('Y'), 1991) as $year) {
            $years[(string) $year] = (string) $year;
        }
        $fields = [
            new \form\field_select('type', options: [
                league_table::PRESET_Main => 'Main',
                league_table::PRESET_Class1 => 'Class1',
                league_table::PRESET_Class5 => 'Class5',
                league_table::PRESET_Foot => 'Foot',
                league_table::PRESET_Aero => 'Aero',
                league_table::PRESET_Winch => 'Winch',
                league_table::PRESET_Defined => 'Defined',
                league_table::PRESET_Winter => 'Winter',
                league_table::PRESET_Female => 'Female',
                league_table::PRESET_Club => 'Club',
                league_table::PRESET_ClubOfficial => 'Club (Official)',
                league_table::PRESET_TopTens => 'Top Tens',
                league_table::PRESET_TopTensNoMultiplier => 'Top Tens (1x)',
                league_table::PRESET_Pilot => 'Pilot',
                league_table::PRESET_Hangies => 'Hangies',
                league_table::PRESET_Records => 'Records',
                league_table::PRESET_Dales => 'Dales',
            ], label: 'League Type', required: false,),
            new \form\field_select('year', options: $years, label: 'Year', default: date('Y',), required: false,),
            new \form\field_link('pilot', label: 'Pilot:', link_module: pilot::class, link_field: 'name', options: new tableOptions(order: 'name ASC'), required: false, disabled: true,),
            new \form\field_boolean('no_min', label: 'No Minimum Distance', required: false,),
            new \form\field_boolean('split_classes', label: 'Split Class 1 & 5', required: false,),
            new \form\field_boolean('glider_mode', label: 'Score gliders not pilots', required: false,),
        ];

        parent::__construct($fields);
        $this->id = 'basic_tables';
        $this->wrapper_class[] = 'basic_tables_wrapper';
        $this->submit = 'Generate';
        $this->get_field_from_name('year')->value = date('Y');
        $this->shroud = '';
        //$this->h2 = 'Options';
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
            node::create('button.btn.btn-default', $this->submit_attributes, $this->submit) . node::create('a.form_toggle', ['dataShow' => 'advanced_tables_wrapper'], 'Advanced View')
        );
        return $field;
    }

    public function get_html(): string
    {
        core::$inline_script[] = '$("#' . $this->id . ' #type").change(function() {
            if($(this).val() == 10) {
                $("#' . $this->id . ' #pilot").attr("disabled", false);
            } else {
                $("#' . $this->id . ' #pilot").attr("disabled", true);
            }
        });';
        return parent::get_html();
    }

    public function set_from_options(league_table $options): void
    {
        foreach ((array) $options as $key => $value) {
            if ($this->has_field((string) $key)) {
                $this->$key = $value;
            }
        }
        if (!$options->minimum_score) {
            $this->no_min = true;
        }
        switch ($options->layout) {
            case league_table::LAYOUT_PILOT_LOG:
                $this->type = league_table::PRESET_Pilot;
                break; 
            case league_table::LAYOUT_TOP_TEN:
                if ($options->no_multipliers) {
                    $this->type = league_table::PRESET_TopTensNoMultiplier;
                } else {
                    $this->type = league_table::PRESET_TopTens;
                }
                break;
            case league_table::LAYOUT_CLUB:
                if ($options->official) {
                    $this->type = league_table::PRESET_ClubOfficial;
                } else {
                    $this->type = league_table::PRESET_Club;
                }
                break;
            case league_table::LAYOUT_RECORDS:
                $this->type = league_table::PRESET_Records;
                break;
        }
        if (!$this->type) {
            if ($options->gender == 2) {
                $this->type = league_table::PRESET_Female;
            } else if ($options->glider_class == 1) {
                $this->type = league_table::PRESET_Class1;
            } else if ($options->glider_class == 5) {
                $this->type = league_table::PRESET_Class5;
            } else if ($options->launches == [1]) {
                $this->type = league_table::PRESET_Foot;
            } else if ($options->launches == [2]) {
                $this->type = league_table::PRESET_Aero;
            } else if ($options->launches == [3]) {
                $this->type = league_table::PRESET_Winch;
            } else if ($options->defined) {
                $this->type = league_table::PRESET_Defined;
            } else if ($options->winter) {
                $this->type = league_table::PRESET_Winter;
            }
        }
        if ($options->pilot_id) {
            $this->get_field_from_name('pilot')->disabled = false;
            $this->pilot = $options->pilot_id;
        }
    }

    public function do_submit(): bool
    {
        $table = new _model\league_table(); 
        $table->use_preset($this->type, $this->year);
        if ($this->type == 10) {
            $table->pilot_id = $this->pilot;
        }
        $table->year = $this->year;
        if ($this->no_min) {
            $table->minimum_score = 0;
        }
        if ($this->glider_mode) {
            $table->glider_mode = true;
        }
        if ($this->split_classes) {
            $table->split_classes = true;
        }
        ajax::update($table->get_table());
        return true;
    }
}
