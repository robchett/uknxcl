<?php

namespace module\cms\model;

use classes\table;
use classes\interfaces\model_interface;

class _cms_user implements model_interface {
    use table;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $uid,
        public int $last_login,
        public string $last_login_ip,
        public string $title,
        public string $password,
    )
    {
    }



}
 