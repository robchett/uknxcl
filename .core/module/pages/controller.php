<?php
namespace core\module\pages;

use classes\get;
use classes\module;
use classes\push_state;
use module\pages\object\page;

abstract class controller extends module {

    /** @var page */
    public $current;


    public function __controller(array $path) {
        if (isset($path[0])) {
            $this->current->do_retrieve_from_id(array(), $path[0]);
        }
        if (!$this->current->pid) {
            get::header_redirect('/latest');
        } else if (uri != $this->current->get_url()) {
            get::header_redirect($this->current->get_url());
        }
        parent::__controller($path);
    }

    public function __construct() {
        $this->current = new page();
    }

    public function ajax_load() {
        $this->current->do_retrieve_from_id(array(), $_REQUEST['page']);
        parent::ajax_load();
    }

    public function get_push_state() {
        $push_state = new push_state();
        $push_state->url = $this->current->get_url();
        $push_state->title = $this->current->nav_title;
        $push_state->data = (object) array(
            'url' => $push_state->url,
            'module' => get_class($this),
            'page' => $this->current->pid,
            'act' => 'ajax_load',
            'id' => '#' . $this->view_object->get_page_selector(),
        );
        $push_state->push = !isset($_REQUEST['is_popped']) ? true : !$_REQUEST['is_popped'];
        return $push_state;
    }
}
