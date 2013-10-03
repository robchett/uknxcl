<?php

namespace core\classes;

abstract class view {

    public $module;

    /**
     * @return \html\node
     */
    public abstract function get_view();
}
