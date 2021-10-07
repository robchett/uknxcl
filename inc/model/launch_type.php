<?php

namespace model;

use classes\table;
use classes\interfaces\model_interface;

class launch_type implements model_interface {
    use table;

    const WINCH = 3;
    const AERO = 2;
    const FOOT = 1;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $lid,
        public string $title,
        public string $fn,
    )
    {
    }
}