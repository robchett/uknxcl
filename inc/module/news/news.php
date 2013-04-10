<?php

class news extends core_module {

    public $page = 'news';

    public function get() {
        $html = '';
        $articles = article::get_all(array('aid', 'date', 'title', 'poster', 'post', 'snippet',), array('order' => 'date DESC', 'where' => 'parent_aid=0'));
        $articles->iterate(function ($article) use (&$html) {
                $html .= $article->get_cell();
            }
        );
        return $html;
    }

}
