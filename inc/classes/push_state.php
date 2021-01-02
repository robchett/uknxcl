<?php

namespace classes;

use classes\push_state_data as _data;
use core;
use JetBrains\PhpStorm\Pure;

class push_state {

    const REPLACE = 1;
    const PUSH = 2;
    public string $url = '';
    public string $title = '';
    public int $type;
    public int $push = 0;
    /**
     * @var push_state_data
     */
    public push_state_data $data;


    #[Pure]
    public function __construct() {
        $this->data = new _data();
        $this->data->actions = [];
        $this->type = self::PUSH;
    }

    public function get() {
        $data = json_encode($this->data);
        $script = '$.fn.ajax_factory.states["' . $this->url . '"] = ' . $data . ';';
        if ($this->type == self::PUSH) {
            $script .= 'window.history.pushState(' . $data . ', "","' . $this->url . '");';
        } else {
            $script .= 'window.history.replaceState(' . $data . ', "","' . $this->url . '");';
        }
        core::$inline_script[] = $script;
    }
}
