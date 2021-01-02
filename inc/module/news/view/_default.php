<?php

namespace module\news\view;

use module\news\model\article;
use template\html;

class _default extends html {

    function get_view(): string {
        $articles = article::get_all(['aid', 'date', 'title', 'poster', 'post', 'snippet',], ['order' => 'date DESC', 'where' => 'parent_aid=0']);
        return "
<div id='list_wrapper'>
    {$articles->reduce(fn($_, article $article) => "$_
    <article id='article{$article->aid}' class='callout callout-primary'>
        <div class='title'>
            <span class='date'>{$article->format_date($article->date, 'Y/m/d')}</span>
            <strong class='name'>{$article->title}</strong>
            <span class='author'>By: {$article->poster}</span>
        </div>
        <div class='content editable_content'>
            {$article->get_snippet()}
            <a class='button' href='{$article->get_url()}'>Read more</a>
        </div>
    </article>")}
</div>";
    }
}
