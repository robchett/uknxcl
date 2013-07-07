<?php

class post_view extends view {
    public function get_view() {
        $html = '<div id="article_wrapper"><article><h2>' . $this->module->current->title . '</h2>' . $this->module->current->post . '</article><a href="#" class="news_back button">Back to news</a></div>';
        $script = '$(".news_back").click(function() {page("/news", {module:"news",act:"ajax_load"})});';
        core::$inline_script[] = $script;
        return $html;
    }

    public function get_page_selector() {
        return get_class($this->module) . '-' . $this->module->current->aid;
    }
}
