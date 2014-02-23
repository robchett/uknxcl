<?php
namespace module\converter;

use classes\module;

class controller extends module {

    public function __controller(array $path) {
        \core::$page_config->title_tag = 'Converter';
        $this->view = '_default';
        if (isset($path[1]) && !empty($path[1])) {
            $this->view = $path[1];
        }
        parent::__controller($path);
    }

}
 