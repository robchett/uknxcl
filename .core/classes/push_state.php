<?php

class push_state {

    const REPLACE = 1;
    const PUSH = 2;
    public $url = '';
    public $title = '';
    public $type;


    public function __construct() {
        $this->data = new stdClass();
        $this->type = self::PUSH;
    }

    public function get() {
        if ($this->type == self::PUSH) {
            core::$inline_script[] = 'window.history.pushState(' . json_encode($this->data) . ', "",
            "' . $this->url . '")';
        } else {
            core::$inline_script[] = 'window.history.replaceState(' . json_encode($this->data) . ', "",
            "' . $this->url . '")';
        }
    }

}
