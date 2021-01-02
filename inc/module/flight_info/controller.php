<?php

namespace module\flight_info;

use classes\get;
use classes\interfaces\model_interface;
use classes\module;
use model\flight;

class controller extends module {

    /** @var flight current */
    public model_interface $current;

    public function __controller(array $path) {
        if (isset($path[1])) {
            $this->view = 'flight';
            $this->current = new flight();
            $this->current->do_retrieve(flight::$default_fields, ['where_equals' => ['fid' => $path[1]], 'join' => flight::$default_joins]);
            if (!$this->current->fid) {
                get::header_redirect('/flight_info');
            }
        }
        parent::__controller($path);
    }

}