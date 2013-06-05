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

    public function get_page_selector() {
        return '#pages-' . $this->current->pid;
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
