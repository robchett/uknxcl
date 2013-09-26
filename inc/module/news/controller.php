<?php
namespace news;
class controller extends \core_module {

    /** @var  article */
    public $current;

    public function __controller(array $path) {
        if (count($path) > 1) {
            $this->set_current($path[1]);
            if ($this->current->aid) {
                $this->view = 'post';
            }
        }
        parent::__controller($path);
    }

    public function show_full() {
        $this->view = 'post';
        $this->set_view();
        $this->set_current($_REQUEST['page']);
        if ($this->current->aid) {
            $this->ajax_load();
        }
    }

    public function set_current($aid) {
        $this->current = new article();
        $this->current->do_retrieve_from_id(array(), $aid);
    }

}
