<?php

namespace model;

use classes\ajax;
use classes\tableOptions;
use form\form;

class pilot extends scorable
{

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $pid,
        public string $name,
        public string $bhpa_no,
        public int $prid,
        public pilot_rating $pilot_rating,
        public int $gid,
        public gender $gender,
        public string $email,
    ) {
    }
}
