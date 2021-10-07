<?php

namespace module\planner\model;

use classes\table;
use classes\interfaces\model_interface;

class waypoint_group implements model_interface {
    use table;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $wgid,
        public string $title,
    )
    {
    }
}
