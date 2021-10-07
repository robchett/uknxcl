<?php

namespace module\comps;

use classes\ajax;
use classes\interfaces\model_interface;
use classes\module;
use module\comps\model\comp;
use module\comps\view\comp as ViewComp;
use module\comps\view\comp_list;
use module\comps\view\create;

class controller extends module {

    /** @param string[] $path */
    public function __construct(array $path) {
        if (isset($path[1]) && ($comp = comp::getFromId((int) $path[1]))) {
            $this->view_object = new ViewComp($this, $comp); 
            return;
        }
        if (isset($path[1]) && $path[1] == 'create' && ($comp = comp::getFromId((int) $path[2]))) {
            $this->view_object = new create($this, $comp);
            return;
        }
        $this->view_object = new comp_list($this, false);
        return;
    }
}
