<?php
namespace news;
class _default_view extends \view {
    public function get_view() {
        $html = '<div id="list_wrapper">';
        $articles = article::get_all(array('aid', 'date', 'title', 'poster', 'post', 'snippet',), array('order' => 'date DESC', 'where' => 'parent_aid=0'));
        //$articles->iterate(function ($article) use (&$html) {
        /** @var $article article */
        foreach ($articles as $article) {
            $html .= $article->get_cell();
        }
        //);
        $html .= '</div><div id="article_wrapper"></div>';
        return $html;
    }
}
