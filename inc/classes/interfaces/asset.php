<?php

namespace classes\interfaces;

abstract class asset {

    public string $content_type = 'text/plain';

    abstract public function compile();

    abstract public function add_files($files);

    abstract public function add_resource_root($root);

}
 