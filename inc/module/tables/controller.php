<?php
namespace module\tables;

use classes\module;

class controller extends module {

    public $page = 'tables';

    public function __controller(array $path) {
        $layout = '';
        $end_count = 1;
        if (isset($path[1]) && file_exists(root . '/inc/module/tables/view/' . $path[1] . '.php')) {
            $this->view = $path[1];
            $end_count++;
        }
        $this->current = new  object\league_table();
        if (isset($path[$end_count]) && !strstr($path[$end_count], '-')) {
            $layout = $path[$end_count];
        }
        if (isset($path[$end_count])) {
            $this->current->set_default(object\league_table::decode_url(end($path)));
        }
        if ($layout) {
            $this->current->set_layout_from_url($layout);
        }
        parent::__controller($path);
    }
}
