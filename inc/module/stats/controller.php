<?php

namespace module\stats;

use classes\module;
use model\flight;
use module\stats\view\_default;

/** @extends module<flight> */
class controller extends module {

    public function get_stats(): array {
        $this->view = _default::class;
        return flight::get_statistics();
    }
}
