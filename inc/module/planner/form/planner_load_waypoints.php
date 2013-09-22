<?php
namespace planner;

use form\form;

class planner_load_waypoints extends form {

    public $wgid;

    public function __construct() {
        parent::__construct([
                form::create('field_link', 'wgid')
                    ->set_attr('label', 'Waypoint Set')
                    ->set_attr('link_field', 132)
                    ->set_attr('link_module', 21)
            ]
        );
        $this->submit = 'Load';
        $this->id = 'planner_load_waypoints';
    }

    public function do_submit() {
        if (parent::do_submit()) {
            $points = waypoint_array::get_all(array('lat', 'lon'), array('where_equals' => array('wgid' => $this->wgid)));
            $js = '';
            //$points->iterate(function ($point) use (&$js) {
            /** @var waypoint $point */
            foreach ($points as $point) {
                $js .= $point->get_js();
            }
            //);
            \ajax::add_script($js);
        }

    }
}
