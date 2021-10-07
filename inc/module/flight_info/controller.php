<?php

namespace module\flight_info;

use classes\get;
use classes\interfaces\model_interface;
use classes\module;
use model\flight;
use module\flight_info\view\flight as ViewFlight;

class controller extends module {

    /** @param string[] $path */
    public function __construct(array $path) {
        if (isset($path[1])) {
            $this->view_object = new ViewFlight($this, flight::getFromId((int) $path[1]));
        }
        parent::__construct($path);
    }

}