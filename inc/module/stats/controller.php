<?php
namespace module\stats;

use classes\module;
use object\flight;

class controller extends module {


    public function get_stats() {
        return flight::get_statistics();
    }
}
