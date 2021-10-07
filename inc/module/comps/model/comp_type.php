<?php

namespace module\comps\model;

use classes\table;
use classes\interfaces\model_interface;

class comp_type implements model_interface {
    use table;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $ctid,
        public string $title,
    )
    {
    }
}
