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
        $years = array('all_time' => 'All Time');
        foreach (range(date('Y'), 1991) as $year) {
            $years[$year] = $year;
        }
        $fields = array(
            'type' => form::create('field_select', 'type')
                ->set_attr('options', array(
                        0 => 'Main',
                        14 => 'Class1',
                        13 => 'Class5',
                        1 => 'Foot',
                        2 => 'Aero',
                        3 => 'Winch',
                        5 => 'Defined',
                        4 => 'Winter',
                        6 => 'Female',
                        8 => 'Club',
                        7 => 'Club (Official)',
                        9 => 'Top Tens',
                        15 => 'Top Tens (1x)',
                        10 => 'Pilot',
                        11 => 'Main (3d)',
                        12 => 'Hangies',
                        16 => 'Records',
                    )
                )
                ->set_attr('label', 'League Type'),
            'year' => form::create('field_select', 'year')
                ->set_attr('options', $years)
                ->set_attr('label', 'Year')
                ->set_attr('value', date('Y'))
                ->set_attr('required', true),
            'pilot' => form::create('field_link', 'pilot')
                ->set_attr('label', 'Pilot:')
                ->set_attr('link_module', '\\object\\pilot')
                ->set_attr('link_field', 'name')
                ->set_attr('options', ['order' => 'name ASC'])
                ->set_attr('help', 'Select a pilot to display flight for|Only works if Pilot is selected in Table Type.')
                ->set_attr('required', true)
                ->set_attr('disabled', true),
            'no_min' => form::create('field_boolean', 'no_min')
                ->set_attr('label', 'No Minimum Distance'),
            'split_classes' => form::create('field_boolean', 'split_classes')
                ->set_attr('label', 'Split Class 1 & 5'),
            'glider_mode' => form::create('field_boolean', 'glider_mode')
                ->set_attr('label', 'Score gliders not pilots'),
        );
        /** @var \form\field $field */
        foreach ($fields as $field) {
            $field->set_attr('required', false);
        }

        parent::__construct($fields);
        $this->id = 'basic_tables_form';
        $this->submit = 'Generate';
        $this->post_text = node::create('a.form_toggle', ['data-show' => 'advanced_tables_wrapper'], 'Advanced View');
        $this->get_field_from_name('year')->value = date('Y');
        $this->shroud = '';
        $this->h2 = 'Options';
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
            if (isset($this->fields[$key])) {
                $this->$key = $value;
            }
        }
        if (!$options->minimum_score) {
            $this->no_min = true;
        }
        if ($options->pilot_id) {
            unset($this->get_field_from_name('pilot')->attributes['disabled']);
        }
    }

    public function do_submit() {
        if (parent::do_submit()) {
            $table = new _object\league_table();
            $table->use_preset($this->type);
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
            get::header_redirect($table->get_url() . '?module=core&act=load_page');
        }
    }
}
