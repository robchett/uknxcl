<?php

namespace model;

use classes\table;
use classes\interfaces\model_interface;

class gender implements model_interface {
    use table;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $gid,
        public string $title,
        public string $short,
    )
    {
    }

}
