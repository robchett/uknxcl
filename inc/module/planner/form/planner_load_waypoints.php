<?php

namespace module\planner\form;

use classes\ajax;
use classes\tableOptions;
use form\form;
use module\planner\model;
use module\planner\model\waypoint_group;

class planner_load_waypoints extends form
{

    public int $wgid;

    public function __construct()
    {
        parent::__construct([
            new \form\field_link('wgid', label: 'Waypoint Set', link_field: 'title', link_module: waypoint_group::class,)
        ]);
        $this->submit = 'Load';
        $this->id = 'planner_load_waypoints';
    }

    public function do_submit(): bool
    {
        $points = model\waypoint::get_all(new tableOptions(where_equals: ['wgid' => $this->wgid])); 
        $js = $points->reduce(fn (string $acc, model\waypoint $point): string => $acc . $point->get_js(), "");
        ajax::add_script($js);
        return true;
    }
}
