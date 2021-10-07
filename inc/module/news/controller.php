<?php

namespace module\news;

use classes\interfaces\model_interface;
use classes\module;
use module\news\model\article;
use module\news\view\post;

class controller extends module {

    /** @param string[] $path */
    public function __construct(array $path) {
        if (count($path) > 1 && ($post = article::getFromId((int) $path[1]))) {
            $this->view_object = new post($this, $post); 
        } else {
            $this->view_object = new view\_default($this, false);
        }
        parent::__construct($path); 
    }

}
