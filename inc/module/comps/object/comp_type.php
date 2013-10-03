<?php
namespace module\comps\object;

use core\classes\table;
use traits\table_trait;

class comp_type extends table {

    use table_trait;

    public $table_key = 'ctid';
    public static $module_id = '23';

}
