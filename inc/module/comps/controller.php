<?php

namespace comps;
class controller extends \core_module {

    public $page = 'comp';
    /** @var comp current */
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
        /** @var $comp comp */
        foreach ($comps as $comp) {
            $comp->do_zip_to_comp();
        }
        //});
    }

}
