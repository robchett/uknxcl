<?php
namespace module\planner\object;

use classes\table;
use traits\table_trait;

class waypoint_group extends table {

    use table_trait;

    public $table_key = 'wgid';
    public static $module_id = 21;

}
