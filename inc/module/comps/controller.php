<?php

namespace module\comps;

use classes\interfaces\model_interface;
use classes\module;
use module\comps\model\comp;

class controller extends module {

    /** @var comp current */
    public model_interface $current;

    public function __controller(array $path) {
        $this->view = 'comp_list';
        $this->current = new comp();
        if (isset($path[1]) && $this->current->do_retrieve_from_id([], $path[1])) {
            $this->view = 'comp';
        }
        if (isset($path[1]) && $path[1] == 'create' && $this->current->do_retrieve_from_id([], $path[2])) {
            $this->view = 'create';
        }
        parent::__controller($path);
    }

    public function ajax_load() {
        if (isset($_REQUEST['cid'])) {
            $this->current = new comp();
            if (isset($_REQUEST['cid'])) {
                $this->current->do_retrieve_from_id([], $_REQUEST['cid']);
            }
            if ($this->current->cid) {
                $this->view = 'comp';
            }
        }
        parent::ajax_load();
    }
}
