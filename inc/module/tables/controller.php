<?php

namespace module\tables;

use classes\module;
use module\tables\model\league_table;
use module\tables\view\_default;
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
        $layout = '';
        if (isset($path[$end_count]) && !strstr($path[$end_count], '-')) {
            $layout = $path[$end_count];
        }
        $options = isset($path[$end_count]) ? model\league_table::decode_url(end($path)) : [];
        $this->view_object = new $view($this, model\league_table::fromUrl($layout, $options)); 
        parent::__construct($path);
    }
}
