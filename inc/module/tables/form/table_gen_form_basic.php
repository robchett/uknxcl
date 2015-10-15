<?php
namespace module\tables\form;

use classes\get;
use form\form;
use html\node;
use module\tables\object as _object;

class table_gen_form_basic extends form {

    public $glider_mode;
    public $no_min;
    public $pilot;
    public $split_classes;
    public $type;
    public $year;

    public function __construct() {
        $years = ['all_time' => 'All Time'];
        foreach (range(date('Y'), 1991) as $year) {
            $years[$year] = $year;
        }
        $fields = [
            form::create('field_select', 'type')
                ->set_attr('options', [
                        0  => 'Main',
                        14 => 'Class1',
                        13 => 'Class5',
                        1  => 'Foot',
                        2  => 'Aero',
                        3  => 'Winch',
                        5  => 'Defined',
                        4  => 'Winter',
                        6  => 'Female',
                        8  => 'Club',
                        7  => 'Club (Official)',
                        9  => 'Top Tens',
                        15 => 'Top Tens (1x)',
                        10 => 'Pilot',
                        12 => 'Hangies',
                        16 => 'Records',
                    ]
                )
                ->set_attr('label', 'League Type'),
            form::create('field_select', 'year')
                ->set_attr('options', $years)
                ->set_attr('label', 'Year')
                ->set_attr('value', date('Y'))
                ->set_attr('required', true),
            form::create('field_link', 'pilot')
                ->set_attr('label', 'Pilot:')
                ->set_attr('link_module', '\\object\\pilot')
                ->set_attr('link_field', 'name')
                ->set_attr('options', ['order' => 'name ASC'])
                ->set_attr('help', 'Select a pilot to display flight for|Only works if Pilot is selected in Table Type.')
                ->set_attr('required', true)
                ->set_attr('disabled', true),
            form::create('field_boolean', 'no_min')
                ->set_attr('label', 'No Minimum Distance'),
            form::create('field_boolean', 'split_classes')
                ->set_attr('label', 'Split Class 1 & 5'),
            form::create('field_boolean', 'glider_mode')
                ->set_attr('label', 'Score gliders not pilots'),
        ];
        /** @var \form\field $field */
        foreach ($fields as $field) {
            $field->set_attr('required', false);
        }

        parent::__construct($fields);
        $this->id = 'basic_tables';
        $this->wrapper_class[] = 'basic_tables_wrapper';
        $this->submit = 'Generate';
        $this->get_field_from_name('year')->value = date('Y');
        $this->shroud = '';
        //$this->h2 = 'Options';
    }

    /**
     * @return node
     */
    public function get_submit() {
        if ($this->has_submit) {
            $field = node::create('div.form-group.submit-group div.col-md-offset-' . $this->bootstrap[0] . '.col-md-' . $this->bootstrap[1], [], [
                node::create('button.btn.btn-default', $this->submit_attributes, $this->submit),
                node::create('a.form_toggle', ['data-show' => 'advanced_tables_wrapper'], 'Advanced View')
            ]);
            if (!$this->submittable) {
                $field->add_attribute('disabled', 'disabled');
            }
            return $field;
        }
        return node::create('');
    }

    public function get_html() {
        \core::$inline_script[] = '$("#' . $this->id . ' #type").change(function() {
            if($(this).val() == 10) {
                $("#' . $this->id . ' #pilot").attr("disabled", false);
            } else {
                $("#' . $this->id . ' #pilot").attr("disabled", true);
            }
        });';
        return parent::get_html();
    }

    public function set_from_options(_object\league_table_options $options) {
        foreach ($options as $key => $value) {
            if ($this->has_field($key)) {
                $this->$key = $value;
            }
        }
        if (!$options->minimum_score) {
            $this->no_min = true;
        }
        switch ($options->layout) {
            case \module\tables\object\league_table_options::LAYOUT_PILOT_LOG :
                $this->type = 10;
                break;
            case \module\tables\object\league_table_options::LAYOUT_TOP_TEN :
                if ($options->no_multipliers) {
                    $this->type = 15;
                } else {
                    $this->type = 9;
                }
                break;
            case \module\tables\object\league_table_options::LAYOUT_CLUB :
                if ($options->official) {
                    $this->type = 7;
                } else {
                    $this->type = 8;
                }
                break;
            case \module\tables\object\league_table_options::LAYOUT_RECORDS :
                $this->type = 16;
                break;
        }
        if (!$this->type) {
            if ($options->gender == 2) {
                $this->type = 6;
            } else if ($options->glider_class == 1) {
                $this->type = 14;
            } else if ($options->glider_class == 5) {
                $this->type = 13;
            } else if ($options->launches == [1]) {
                $this->type = 1;
            } else if ($options->launches == [2]) {
                $this->type = 2;
            } else if ($options->launches == [3]) {
                $this->type = 3;
            } else if ($options->defined) {
                $this->type = 5;
            } else if ($options->winter) {
                $this->type = 4;
            }
        }
        if ($options->pilot_id) {
            $this->get_field_from_name('pilot')->disabled = false;
            $this->pilot = $options->pilot_id;
        }
    }

    public function do_submit() {
        $table = new _object\league_table();
        $table->use_preset($this->type, $this->year);
        if ($this->type == 10) {
            $table->options->pilot_id = $this->pilot;
        }
        $table->set_year($this->year);
        if ($this->no_min) {
            $table->options->minimum_score = 0;
        }
        if ($this->glider_mode) {
            $table->options->glider_mode = true;
        }
        if ($this->split_classes) {
            $table->options->split_classes = true;
        }
        get::header_redirect($table->get_url() . '?module=core&act=load_page&form=' . $_REQUEST['ajax_origin']);
    }
}
