<?php

class table_gen_form_basic extends form {

    public $glider_mode;
    public $no_min;
    public $pilot;
    public $split_classes;
    public $type;
    public $year;

    public function __construct() {
        $years = array(0 => 'All Time');
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
            'pilot' => form::create('field_select', 'pilot')
                ->set_attr('label', 'Pilot:')
                ->set_attr('options', alphabeticalise::pilot_array())
                ->set_attr('help', 'Select a pilot to display flight for|Only works if Pilot is selected in Table Type.')
                ->set_attr('required', true),
            'no_min' => form::create('field_boolean', 'no_min')
                ->set_attr('label', 'No Minimum Distance'),
            'split_classes' => form::create('field_boolean', 'split_classes')
                ->set_attr('label', 'Split Class 1 & 5'),
            'glider_mode' => form::create('field_boolean', 'glider_mode')
                ->set_attr('label', 'Score gliders not pilots'),
        );
        /** @var field $field */
        foreach ($fields as $field) {
            $field->set_attr('required', false);
        }

        parent::__construct($fields);
        $this->id = 'basic_tables_form';
        $this->submit = 'Generate';
        $this->post_text = '<a class="form_toggle" data-show="advanced_tables_wrapper">Advanced View</a>';
        $this->get_field_from_name('year')->value = date('Y');
        $this->shroud = '';
        $this->h2 = 'Options';
    }

    public function do_submit() {
        if (parent::do_submit()) {
            $table = new league_table();
            $table->use_preset($this->type);
            if ($this->type == 10) {
                $table->pid = $this->pilot;
            }
            if ($this->year)
                $table->set_year($this->year);
            if ($this->no_min)
                $table->MinScore = 0;
            if ($this->glider_mode)
                $table->set_glider_view();
            if ($this->split_classes)
                $table->split_classes = true;

            ajax::update('<div id="generated_tables">' . $table->get_table() . '</div>');
        }
    }
}
