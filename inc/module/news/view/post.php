<?php

class post_view extends view {
    public function get_view() {
        $html = '<div id="article_wrapper"><article><h2>' . $this->module->current->title . '</h2>' . $this->module->current->post . '</article><a href="/news" class="news_back button">Back to news</a></div>';
        return $html;
    }
}
