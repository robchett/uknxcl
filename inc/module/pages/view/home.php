<?php

namespace module\pages\view;

use classes\module;
use classes\tableOptions;
use module\cms\controller;
use module\pages\model\page;

class home extends _default
{

    public function get_view(): string
    {
        $pages = page::get_all(new tableOptions(order: 'position', where: 'pid != 12'));
        return "<div class='content'>{$this->current->body}</div>
<div id='page_list'>
    {$pages->reduce(fn (string $_, page$page): string => $_ . "
    <div class='page'>
        <a href='{$page->get_url()}'>
            <h2>
                <span class='glyphicon glyphicon-{$page->icon}'></span><br/>
                {$page->title}
            </h2>
            <p>{$page->info}</p>
        </a>
    </div>", '')}
</div>";
    }

    public function get_page_selector(): string
    {
        return 'pages-' . $this->current->pid;
    }
}
