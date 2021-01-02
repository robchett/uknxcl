<?php

namespace module\stats;

use classes\module;
use model\flight;

class controller extends module {


    public function get_stats(): array {
        return flight::get_statistics();
    }
}
