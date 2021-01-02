<?php

namespace module\news\view;

use classes\module;
use module\news\controller;
use template\html;

class post extends html {

    /** @var controller */
    public module $module;

    public function get_view(): string {
        $article = $this->module->current;
        return "
<div id='article_wrapper' class='editable_content'>
    <article>
        <div class='page-header'>
            <h1>{$article->title}</h1>
            <span class='author'>By: {$article->poster}</span>
        </div>
        {$article->post}
    </article>

    <a class='news_back button' href='/news' style='margin-top: 10px'>Back to news</a>
</div>";
    }
}
