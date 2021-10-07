<?php

namespace module\latest;

use classes\module;

class controller extends module {

    /** @param string[] $path */
    public function __construct(array $path)
    {
        $this->view_object = new view\_default($this, false);
        parent::__construct($path);
    }

}