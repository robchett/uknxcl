<?php

namespace object;

use core\classes\table;
use traits\table_trait;

class gender extends table {

    use table_trait;

    public $table_key = 'gid';
    public static $module_id = 14;


}
