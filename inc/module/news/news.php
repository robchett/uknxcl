<?php

class news extends core_module {

    public $page = 'news';

    public function get() {
        $html = '<div id="list_wrapper">';
        $articles = article::get_all(array('aid', 'date', 'title', 'poster', 'post', 'snippet',), array('order' => 'date DESC', 'where' => 'parent_aid=0'));
        $articles->iterate(function ($article) use (&$html) {
                $html .= $article->get_cell();
            }
        );
        $html .= '</div><div id="article_wrapper"></div>';
        return $html;
    }

    public function convert_all(){
        $articles = article::get_all(array('aid', 'date', 'title', 'poster', 'post', 'snippet',), array('order' => 'date DESC', 'where' => 'parent_aid=0'));
        $articles->iterate(function ($article) use (&$html) {
                $article->post = parser::p($article->post);
                $article->do_save();
            }
        );
    }

}
