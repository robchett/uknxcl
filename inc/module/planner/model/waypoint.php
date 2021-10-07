<?php

namespace module\planner\model;

use classes\table;
use classes\interfaces\model_interface;
use classes\tableOptions;

class waypoint  implements model_interface {
    use table;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $wid,
        public int $wgid,
        public waypoint_group $waypoint_group,
        public string $title,
        public float $lon,
        public float $lat,
    )
    {
    }

    public function get_js(): string {
        return 'map.planner.add_marker(' . $this->lat . ',' . $this->lon . ');';
    }
}