<?php
namespace stats;
class controller extends \core_module {


    public function get_stats() {
        return \flight::get_statistics();
    }
}
