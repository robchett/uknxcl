<?php

namespace module\pages;

use classes\ajax;
use classes\get;
use classes\interfaces\model_interface;
use classes\module;
use classes\push_state;
use module\pages\model\page;
use module\pages\view\_default;
use module\pages\view\home;

class controller extends module
{

    public static int $homepage_id = 12;

    /** @param string[] $path */
    public function __construct(array $path)
    {
        $id = (int) ($path[0] ?? self::$homepage_id);
        if ($page = page::getFromId($id)) {
            $view = $page->pid == self::$homepage_id ? home::class : _default::class;
            $this->view_object = new $view($this, $page);
        } else {
            get::header_redirect('/');
        }
        parent::__construct($path);
    }
}
