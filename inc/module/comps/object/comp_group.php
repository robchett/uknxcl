<?php
namespace module\comps\object;

use core\classes\table;
use traits\table_trait;

class comp_group extends table {

    use table_trait;

    public $table_key = 'cgid';
    public static $module_id = 18;

}
