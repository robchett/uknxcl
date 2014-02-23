<?php
namespace module\news\view;

use classes\view;
use html\node;
use module\news\object\article;

class _default extends \template\html {

    public function get_view() {
        $articles = article::get_all(['aid', 'date', 'title', 'poster', 'post', 'snippet',], ['order' => 'date DESC', 'where' => 'parent_aid=0']);
        $html = node::create('div#list_wrapper', [],
            $articles->iterate_return(function (article $article) {
                    return $article->get_cell();
                }
            ) . node::create('div#article_wrapper', [])
        );
        return $html;
    }
}
