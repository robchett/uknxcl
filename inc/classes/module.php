<?php

namespace classes;

use classes\ajax;
use classes\get;
use classes\interfaces\model_interface;
use classes\interfaces\page_template_interface;
use core;
use module\pages\model\page;
use module\pages\view\_default;

class module {

    /** @var string[] */
    public static array $page_fields_to_retrieve = ['pid', 'body', 'title'];
    /** @var class-string<page_template_interface> */
    public string $view = _default::class;
    public int $page = 1;
    public int $pid = 0;
    public int $npp = 50;
    public page_template_interface $view_object;
    public page $page_object;

    /**
     * @param string[] $path
     */
    public function __construct(array $path) {
        if (count($path) > 3 && $path[count($path) - 2] == 'page') {
            if (end($path) == 'all') {
                $this->npp = 99999999;
                $this->page = 1;
            } else {
                $this->page = (int) end($path);
            }
        }
        $this->set_page();
        $this->view_object->add_body_class('module_' . get::__namespace($this, 0), $this->view);
    }

    function set_page(): void {
        if (!$this->pid) {
            $page_object = page::get(new tableOptions(where_equals: ['module_name' => get::__namespace($this, 0)]));
        } else {
            $page_object = page::getFromId($this->pid);
        }
        if ($page_object) {
            $this->page_object = $page_object;
        }
    }

    function get_main_nav(): string {
        $pages = page::get_all(new tableOptions(where: 'nav=1', order: 'position'));
        return $pages->iterate_return(fn (page $page) => strtr("<li class='%class'><a href='{$page->get_url()}'>%title</a></li>", [
            '%class' => $page->pid == core::$singleton->pid ? '.sel' : '',
            '%title' => $page->nav_title ?: $page->title
        ]));
    }
}
