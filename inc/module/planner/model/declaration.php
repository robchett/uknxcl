<?php

namespace module\planner\model;

use classes\ajax;
use classes\table;
use classes\table_form;
use Exception;


/**
 * Class declaration
 * @package planner
 */
class declaration extends table {


    /**
     * @var string
     */
    public string $title;
    /**
     * @var int
     */
    public ?int $did;


    /**
     * @return table_form
     * @throws Exception
     */
    public function get_form(): table_form {
        $form = parent::get_form();#
        $form->action = get_class($this) . ':do_form_submit';
        $form->set_from_request();
        $form->remove_field('did');
        $form->get_field_from_name('parent_did')->hidden = true;
        $form->parent_did = 1;
        $form->get_field_from_name('ftid')->hidden = true;
        $form->get_field_from_name('coordinates')->hidden = true;
        $form->get_field_from_name('pid')->label = 'Pilot';
        $form->get_field_from_name('pid')->options = ['order' => 'pilot.name'];
        $form->h2 = 'Declare a flight';
        $form->date = time();
        return $form;
    }

    /**
     *
     */
    public function do_submit(): bool {
        ajax::add_script('$("#' . $_REQUEST['ajax_origin'] . '").html("<p>Your declaration has been accepted, have a good flight!</p>"); ');
        return true;
    }
}
