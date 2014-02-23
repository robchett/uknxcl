<?php

namespace object;

use classes\table;
use traits\table_trait;

class launch_type extends table {

    use table_trait;

    const WINCH = 3;
    const AERO = 2;
    const FOOT = 1;

    public $lid;
    public $title;
    public $fn;

}