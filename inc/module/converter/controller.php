<?php

namespace module\converter;

use classes\module;
use core;

class controller extends module {

    /** @param string[] $path */
    public function __construct(array $path) {
        $this->view_object = new view\_default($this, false);
        parent::__construct($path);
        $this->view_object->title_tag = 'Converter';
    }

}
 