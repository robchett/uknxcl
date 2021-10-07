<?php

namespace module\planner\model;

use classes\ajax;
use classes\table;
use classes\interfaces\model_interface;
use classes\table_form;
use Exception;
use model\flight_type;
use model\pilot;

/**
 * Class declaration
 * @package planner
 */
class declaration  implements model_interface {
    use table;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $did,
        public string $coordinates,
        public int $date,
        public int $pid,
        public pilot $pilot,
        public int $ftid,
        public flight_type $flight_type,
    )
    {
    }

    /**
     * @return table_form
     * @throws Exception
     */
    public static function get_form(): table_form {
        $form = table::get_form();
        $form->action = get_class() . ':do_form_submit';
        $form->set_from_request();
        $form->remove_field('did');
        $form->get_field_from_name('parent_did')->hidden = true;
        $form->get_field_from_name('ftid')->hidden = true;
        $form->get_field_from_name('coordinates')->hidden = true;
        $form->get_field_from_name('pid')->label = 'Pilot';
        $form->get_field_from_name('pid')->options = ['order' => 'pilot.name'];
        $form->h2 = 'Declare a flight';
        $form->date = time();
        return $form;
    }

    public function do_submit(): bool {
        ajax::add_script('$("#' . $_REQUEST['ajax_origin'] . '").html("<p>Your declaration has been accepted, have a good flight!</p>"); ');
        return true;
    }
}
