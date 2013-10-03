<?php

namespace object;

use core\classes\table;
use traits\table_trait;

class dimension extends table {

    use table_trait;

    public $table_key = 'did';
    public static $module_id = 11;

}

