<?php
namespace module\planner\form;

use classes\ajax;
use form\form;
use module\planner\object;

class planner_load_waypoints extends form {

    public $wgid;

    public function __construct() {
        parent::__construct([
                form::create('field_link', 'wgid')
                    ->set_attr('label', 'Waypoint Set')
                    ->set_attr('link_field', 132)
                    ->set_attr('link_module', '\\module\\planner\\object\\waypoint_group')
            ]
        );
        $this->submit = 'Load';
        $this->id = 'planner_load_waypoints';
    }

    public function do_submit() {
        if (parent::do_submit()) {
            $points = object\waypoint_array::get_all('\module\planner\object\waypoint', array('lat', 'lon'), array('where_equals' => array('wgid' => $this->wgid)));
            $js = '';
            $points->iterate(function (object\waypoint $point) use (&$js) {
                    $js .= $point->get_js();
                }
            );
            ajax::add_script($js);
        }

    }
}

