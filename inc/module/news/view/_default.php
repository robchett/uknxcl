<?php
namespace news;

use html\node;

class _default_view extends \view {
    public function get_view() {
        $articles = article::get_all(array('aid', 'date', 'title', 'poster', 'post', 'snippet',), array('order' => 'date DESC', 'where' => 'parent_aid=0'));
        $html = node::create('div#list_wrapper', [],
            $articles->iterate_return(function (article $article) {
                    return $article->get_cell();
                }
            ) . node::create('div#article_wrapper', [])
        );
        return $html;
    }
}
