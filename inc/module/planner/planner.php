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

    public function do_load_bos() {
        $points = waypoint_array::get_all(array('lat', 'lon'));
        $js = '';
        //$points->iterate(function ($point) use (&$js) {
        foreach ($points as $point) {
            $js .= $point->get_js();
        }
        //);
        ajax::add_script($js);

    }

}
