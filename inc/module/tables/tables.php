<?php

class tables extends core_module {

    public $page = 'tables';

    public function __controller(array $path) {
        if(isset($path[1]) && file_exists(root . '/inc/module/tables/view/' . $path[1] . '.php')) {
            $this->view = $path[1];
        }
        parent::__controller($path);
    }
}
