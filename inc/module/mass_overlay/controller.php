<?php

namespace module\mass_overlay;

use classes\module;
use module\tables\model\league_table;
use module\tables\view\skywings;

class controller extends module {
    
    /** @param string[] $path */
    public function __construct(array $path) {
        $end_count = 1;
        $view = match($path[1] ?? '') {
            'skywings' => skywings::class,
            default => view\_default::class
        };
    
        if (isset($path[1]) && file_exists(root . '/inc/module/tables/view/' . $path[1] . '.php')) {
            $end_count++;
        }
        $current = new  league_table();
        $current->base_url = '/mass_overlay';
        if (isset($path[$end_count])) {
            $current->set_default(league_table::decode_url(end($path)));
        }
        $this->view_object = new $view($this, $current);
        parent::__construct($path);
    }
}