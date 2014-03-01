<?php
namespace module\tables\form;

use classes\get;
use form\form;
use html\node;
use module\tables\object as _object;

class table_gen_form extends form {

    public $no_min;

    public function __construct() {
        $fields = [
            form::create('field_select', 't')
                ->set_attr('label', 'Table Type:')
                ->set_attr('default', '')
                ->set_attr('link', 'table_type')
                ->set_attr('help', '')
                ->set_attr('options', [
                        0 => 'Standard',
                        1 => 'Club',
                        2 => 'Pilot',
                        3 => 'Top Tens',
                        4 => 'Plain List',
                        5 => 'Records',
                    ]
                ),
            form::create('field_link', 'pilot')
                ->set_attr('label', 'Pilot:')
                ->set_attr('link_module', '\\object\\pilot')
                ->set_attr('link_field', 'name')
                ->set_attr('options', ['order' => 'name ASC'])
                ->set_attr('help', 'Select a pilot to display flight for|Only works if Pilot is selected in Table Type.')
                ->add_class('pilot_select')
                ->set_attr('disabled', true),
            form::create('field_string', 'year')
                ->set_attr('label', 'Season:')
                ->set_attr('help', "Choose a specific year 'xxxx' or a range 'xxxx-yyyy' or multiple|'xxxx,yyyy'. These can be combined so ie. a range plus some other|'xxxx-yyyy,zzzz' (note don't add the '').")
                ->set_attr('value', '1991-' . date('Y')),
            form::create('field_select', 'gen')
                ->set_attr('label', 'Gender:')
                ->set_attr('options', [0 => 'Both', 1 => 'Male', 2 => 'Female'])
                ->set_attr('help', 'Shows only flights of one gender.'),
            form::create('field_select', 'def')
                ->set_attr('label', 'Declared:')
                ->set_attr('options', [0 => 'Don\'t Sort', 1 => 'Yes', 2 => 'No'])
                ->set_attr('help', 'Shows only declared or not.'),
            form::create('field_select', 'rgd')
                ->set_attr('label', 'Ridge:')
                ->set_attr('options', [0 => 'Don\'t Sort', 1 => 'Yes', 2 => 'No'])
                ->set_attr('help', 'Shows only flights which were under ridge lift or not |not all ridge lift flights have been marked as so...'),
            form::create('field_select', 'win')
                ->set_attr('label', 'Winter:')
                ->set_attr('options', [0 => 'Don\'t Sort', 1 => 'Winter Only', 2 => 'Summer Only'])
                ->set_attr('help', 'Show only flights in the winter (start of november-end of February) | or the summer season'),
            form::create('field_select', 'c3d')
                ->set_attr('label', 'KML Submitted:')
                ->set_attr('options', [
                    0 => 'Don\'t Sort',
                    3 => 'Yes (3D)',
                    2 => 'Yes (2D)',
                    1 => 'Yes (Both)',
                    4 => 'No'])
                ->set_attr('help', 'Choose what type of track confirmation you want.'),
            form::create('field_select', 'cls')
                ->set_attr('label', 'Glider Class:')
                ->set_attr('options', [0 => 'Don\'t Sort', 1 => 'Class 1', 5 => 'Class 5'])
                ->set_attr('help', 'Show Class 1 (flexwing) or class 5 (rigid)'),
            form::create('field_select', 'Flights')
                ->set_attr('label', '# of Flights')
                ->set_attr('default', '')
                ->set_attr('options', [6 => '6 Flights', 5 => '5 Flights', 4 => '4 Flights'])
                ->set_attr('help', 'Maximum number fo flights which count. Before 2001 only 5 counted. now it is 6'),
            form::create('field_string', 'Min')
                ->set_attr('label', 'Minimum Distance')
                ->set_attr('value', 10)
                ->set_attr('help', 'Exclude flights under this distance (pre-multipliers)'),
            form::create('field_boolean', 'noMulti')
                ->set_attr('label', 'No Multipliers')
                ->set_attr('help', 'Do not add multipliers for qualifying flights, useful for looking up records.'),
            form::create('field_boolean', 'show_top_4')
                ->set_attr('label', 'Top Flights')
                ->set_attr('help', 'Show a sub table with the highest scoring flights in each category.'),
            form::create('field_boolean', 'View')
                ->set_attr('label', 'Official View')
                ->set_attr('help', 'for the maximum number of flights to count at least one defined and|at least one must be undefined. In club mode only the top 4 pilots|will count as well'),
            form::create('field_boolean', 'split')
                ->set_attr('label', 'Split Classes')
                ->set_attr('help', 'Number class 5 and class 1 separately.'),
            form::create('field_boolean', 'HK')
                ->set_attr('label', 'Enable Handicapping')
                ->set_attr('help', 'Handicap flights by the glider type, we need to build a better database of glider for this to be more useful,|as of now we only know what glider are KPL and Rigid |   Handicaps stack, so setting KPL to 0.5 and Rigid to 0.5 will actually score rigids as 0.25 as all rigids are KPL (I guess)'),
            form::create('field_string', 'kp')
                ->set_attr('label', '-&gt; Kingpost')
                ->set_attr('value', 1)
                ->set_attr('help', 'Handicap to set KPL glider'),
            form::create('field_string', 'c5')
                ->set_attr('label', '-&gt; Class 5')
                ->set_attr('value', 1)
                ->set_attr('help', 'Handicap to set Rigid glider'),
            form::create('field_string', 'os')
                ->set_attr('label', 'Flown through')
                ->set_attr('value', '')
                ->set_attr('help', 'OS grids flights must fly through'),
            form::create('field_multi_select', 'launch')
                ->set_attr('label', 'Launch Type')
                ->set_attr('help', 'Include certain launch methods')
                ->set_attr('default', '')
                ->set_attr('value', ['w', 'f', 'a'])
                ->set_attr('options', ['w' => 'Winch', 'f' => 'Foot', 'a' => 'Aerotow']),
            form::create('field_multi_select', 'flight_type')
                ->set_attr('label', 'Launch Type')
                ->set_attr('help', 'Include flight types')
                ->set_attr('default', '')
                ->set_attr('value', ['od', 'or', 'tr', 'go', 'ft'])
                ->set_attr('options', [
                    'od' => 'Open Distance',
                    'go' => 'Goal',
                    'or' => 'Out and Return',
                    'tr' => 'Triangle',
                    'ft' => 'Flat Triangle'])
        ];


        parent::__construct($fields);

        $this->name = 'advTables';
        $this->title = 'Pre Calculation Checks';
        $this->description = '';
        $this->wrapper_class = '.advanced_tables_wrapper';
        $this->id = 'advanced_tables';
        $this->submit = 'Generate';
        $this->post_text = node::create('a.form_toggle', ['data-show' => 'basic_tables_wrapper'], 'Basic');
        $this->h2 = 'Advanced Options';
    }

    public function get_html() {
        \core::$inline_script[] = '$("#' . $this->id . ' #t").change(function() {
            if($(this).val() == 2) {
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
        $table = new _object\league_table();
        $table->set_from_request();
        get::header_redirect($table->get_url() . '?module=core&act=load_page');
    }
}
