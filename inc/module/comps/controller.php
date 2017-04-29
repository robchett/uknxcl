<?php

namespace module\comps;

use classes\module;
use module\comps\object\comp;

class controller extends module {

    public $page = 'comp';
    /** @var \module\comps\object current */
    public $current;

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

    public function do_generate_all() {
        $comps = comp::get_all([]);
        $comps->iterate(function (comp $comp) {
            $comp->do_zip_to_comp();
        }
        );
    }

}
