<?php

namespace model;

use classes\table;
use classes\interfaces\model_interface;

class new_flight_notification implements model_interface {
    use table;

    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $nfid,
        public string $email,
    )
    {
    }
}
