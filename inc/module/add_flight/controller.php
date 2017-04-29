<?php
namespace module\add_flight;

use classes\module;

class controller extends module {

    public $page = 'add_flight';

    public function __controller(array $path) {
        $this->view = 'type_select';
        if (isset($path[1])) {
            $this->view = $path[1];
        }
        parent::__controller($path);
    }

    public function ajax_load() {
        if (isset($_REQUEST['sub'])) {
            $this->view = $_REQUEST['sub'];
        }
        parent::ajax_load();
    }
}
