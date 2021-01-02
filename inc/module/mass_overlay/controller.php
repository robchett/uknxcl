<?php

namespace module\mass_overlay;

use classes\module;
use module\tables\model\league_table;

class controller extends module {
    public function __controller(array $path) {
        $end_count = 1;
        if (isset($path[1]) && file_exists(root . '/inc/module/tables/view/' . $path[1] . '.php')) {
            $this->view = $path[1];
            $end_count++;
        }
        $this->current = new  league_table();
        $this->current->base_url = '/mass_overlay';
        if (isset($path[$end_count])) {
            $this->current->set_default(league_table::decode_url(end($path)));
        }
        parent::__controller($path);
    }
}