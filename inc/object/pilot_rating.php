<?php

namespace object;

use core\classes\table;
use traits\table_trait;

class pilot_rating extends table {

    use table_trait;

    public $table_key = 'prid';
    public static $module_id = 15;

}
