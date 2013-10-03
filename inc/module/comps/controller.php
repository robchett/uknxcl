<?php

namespace module\comps;

use classes\module;
use module\comps\object\comp;

class controller extends module {

    public $page = 'comp';
    /** @var \module\comps\object current */
    public $current;

    public function __controller(array $path) {
        $this->current = new comp();
        if (isset($path[1])) {
            $this->current->do_retrieve_from_id(array(), $path[1]);
        }
        if ($this->current->cid) {
            $this->view = 'comp';
        }
        parent::__controller($path);
    }

    public function ajax_load() {
        if (isset($_REQUEST['cid'])) {
            $this->current = new comp();
            if (isset($_REQUEST['cid'])) {
                $this->current->do_retrieve_from_id(array(), $_REQUEST['cid']);
            }
            if ($this->current->cid) {
                $this->view = 'comp';
            }
        }
        parent::ajax_load();
    }

    public function do_generate_all() {
        $comps = comp::get_all(array());
        $comps->iterate(function (comp $comp) {
                $comp->do_zip_to_comp();
            }
        );
    }

}
