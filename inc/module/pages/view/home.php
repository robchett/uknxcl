<?php

namespace module\pages\view;

use classes\module;
use module\cms\controller;
use module\pages\model\page;

/** @property \module\pages\controller $module */
class home extends _default {

    /** @var controller */
    public module $module;


    public function get_view(): string {
        $pages = page::get_all(['title', 'info', 'module_name', 'fn', 'icon'], ['order' => 'position', 'where' => 'pid != 12']);
        return "<div class='content'>{$this->module->current->body}</div>
<div id='page_list'>
    {$pages->reduce(fn ($_, page $page) => $_ . "
    <div class='page'>
        <a href='{$page->get_url()}'>
            <h2>
                <span class='glyphicon glyphicon-{$page->icon}'></span><br/>
                {$page->title}
            </h2>
            <p>{$page->info}</p>
        </a>
    </div>")}
</div>";
    }

    public function get_page_selector(): string {
        return 'pages-' . $this->module->current->pid;
    }
}
