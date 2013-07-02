<?php

class pages extends core_module {
    /** @var page */
    public $current;


    public function __controller(array $path) {
        if (isset($path[0])) {
            $this->current->do_retrieve_from_id(array(),$path[0]);
        }
        if(!$this->current->pid) {
            get::header_redirect('/latest');
        } else if(uri != $this->current->get_url()) {
            get::header_redirect($this->current->get_url());
        }
        parent::__controller($path);
    }

    public function __construct() {
        $this->current = new page();
    }

    public function get_page_selector() {
        return 'pages-' . $this->current->pid;
    }

    public function ajax_load() {
        $this->current->do_retrieve_from_id(array(),$_REQUEST['page']);
        parent::ajax_load();
    }

    public function get_push_state() {
        $push_state = new push_state();
        $push_state->url = $this->current->get_url();
        $push_state->title = $this->current->nav_title;
        $push_state->data = (object) array(
            'page' => array(
                'url' => $push_state->url,
            ),
            'post' => array(
                'module' => get_class($this),
                'page' => $this->current->pid,
                'act' => 'ajax_load'
            )
        );
        return $push_state;
    }
}
