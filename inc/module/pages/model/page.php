<?php

namespace module\pages\model;

use classes\get;
use classes\table;
use module\pages\controller;


class page extends table {


    public string $nav_title;
    public string $direct_link;
    public bool $nav;
    public int $pid;
    public string $body = '';
    public string $module_name;
    public string $title = '';
    public string $icon;
    /** @var ?string */
    public ?string $info;


    /**
     * @return string
     */
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
