<?php
namespace module\planner\object;

use classes\ajax;
use classes\table;
use traits\table_trait;

/**
 * Class declaration
 * @package planner
 */
class declaration extends table {

    use table_trait;

    /**
     * @var string
     */
    public $title;
    /**
     * @var int
     */
    public $did;


    /**
     * @return \form\form
     */
    public function get_form() {
        $form = parent::get_form();
        $form->action = get_class($this) . ':do_form_submit';
        $form->set_from_request();
        $form->remove_field('parent_did');
        $form->remove_field('did');
        $form->get_field_from_name('ftid')->hidden = true;
        $form->get_field_from_name('coordinates')->hidden = true;
        $form->get_field_from_name('pid')->label = 'Pilot';
        $form->get_field_from_name('pid')->options = ['order' => 'pilot.name'];
        $form->h2 = 'Declare a flight';
        $form->date = time();;
        return $form;
    }

    /**
     *
     */
    public function do_submit() {
        ajax::add_script('$("#' . $_REQUEST['ajax_origin'] . '").html("<p>Your declaration has been accepted, have a good flight!</p>"); ');
    }
}
