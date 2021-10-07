<?php

namespace model;

use classes\interfaces\model_interface;
use classes\table;

class dimension  implements model_interface {
    use table;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $did,
        public string $title,
        public int $dimensions,
    )
    {
    }

}

