<?php
class declaration extends table {
    public $table_key = 'did';
    public static $module_id = 13;

    /* @return declaration_array */
    public static function get_all(array $fields, array $options = array()) {
        return declaration_array::get_all($fields, $options);
    }

    public function get_form(){
        $form = parent::get_form();
        $form->action = 'declaration:do_submit';
        $form->set_from_request();
        $form->remove_field('parent_did');
        $form->remove_field('did');
        $form->get_field_from_name('ftid')->hidden = true;
        $form->get_field_from_name('coordinates')->hidden = true;
        $form->get_field_from_name('pid')->label = 'Pilot';
        $form->get_field_from_name('pid')->options = array('order'=>'pilot.name');
        $form->h2 = 'Declare a flight';
        return $form;
    }

    public function do_submit() {
        if(parent::do_submit()) {
            ajax::add_script('$("#' . $_REQUEST['ajax_origin'] . '").remove(); $.colorbox.resize();colorbox_recenter();');
        }
    }
}

class declaration_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'declaration_iterator');
        $this->iterator = new declaration_iterator($input);
    }

    /* @return declaration */
    public function next() {
        return parent::next();
    }
}

class declaration_iterator extends table_iterator {

    /* @return declaration */
    public function key() {
        return parent::key();
    }
}