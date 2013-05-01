<?php

class pages extends core_module {
    /** @var page */
    public $current;


    public function __controller(array $path) {
        parent::__controller($path);
    }

    public function get() {
        if (isset($_REQUEST['page'])) {
            $this->current = new page(array(), (int) $_REQUEST['page']);
            if ($this->current->pid) {
                return $this->current->body;
            }
        }
        return '';
    }
}
