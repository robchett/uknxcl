<?php

namespace module\pages\model;

use classes\get;
use classes\table;
use classes\interfaces\model_interface;
use module\pages\controller;

class page implements model_interface {
    use table;


    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $pid,
        public string $nav_title,
        public string $direct_link,
        public bool $nav,
        public string $body,
        public string $module_name,
        public string $title,
        public string $icon,
        public string $info,
        public string $fn,
    )
    {
    }
    
    public function get_url(): string {
        if ($this->pid == controller::$homepage_id) {
            return '/';
        }
        if (isset($this->direct_link) && $this->direct_link) {
            return $this->direct_link;
        } else if (!empty($this->module_name)) {
            return '/' . $this->module_name;
        } else {
            return '/' . $this->pid . '/' . get::fn($this->title);
        }
    }
}
