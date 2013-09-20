<?php
namespace add_flight;
class controller extends \core_module {

    public $page = 'add_flight';

    public function __controller(array $path) {
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
