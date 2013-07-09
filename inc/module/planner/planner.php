<?php

class planner extends core_module {

    public $page = 'planner';

    public function get_form() {
        if (isset($_REQUEST['ftid']) && isset($_REQUEST['coordinates'])) {
            $dec = new declaration();
            $form = $dec->get_form();
            jquery::colorbox(array('html' => $form->get_html()->get()));
        }
    }
}
