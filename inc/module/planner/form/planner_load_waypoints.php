<?php

namespace module\planner\form;

use classes\ajax;
use form\form;
use module\planner\model;

class planner_load_waypoints extends form {

    public $wgid;

    public function __construct() {
        parent::__construct([form::create('field_link', 'wgid')->set_attr('label', 'Waypoint Set')->set_attr('link_field', 'title')->set_attr('link_module', \module\planner\model\waypoint_group::class)]);
        $this->submit = 'Load';
        $this->id = 'planner_load_waypoints';
    }

    public function do_submit(): bool {
        $points = model\waypoint::get_all(['lat', 'lon'], ['where_equals' => ['wgid' => $this->wgid]]);
        $js = '';
        $points->iterate(function (model\waypoint $point) use (&$js) {
            $js .= $point->get_js();
        });
        ajax::add_script($js);
        return true;
    }
}

