<?php

namespace module\add_flight;

use classes\ajax;
use classes\module;
use module\add_flight\view\_default;
use template\html;

class controller extends module
{

    /** @param string[] $path */
    public function __construct(array $path)
    {
        $this->view_object = new _default($this, false);
        parent::__construct($path);
    }
}
