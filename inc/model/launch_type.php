<?php

namespace model;

use classes\table;


class launch_type extends table {


    const WINCH = 3;
    const AERO = 2;
    const FOOT = 1;

    public $lid;
    public string $title;
    public $fn;

}