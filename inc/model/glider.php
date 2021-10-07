<?php

namespace model;

use classes\ajax;
use classes\tableOptions;
use form\form;


class glider extends scorable {

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $gid,
        public string $name,
        public int $mid,
        public manufacturer $manufacturer,
        public int $class,
        public bool $kingpost,
        public bool $single_surface,
    )
    {
    }
}

