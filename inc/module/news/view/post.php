<?php
namespace module\news\view;

use classes\view;
use html\node;
use module\news;

class post extends \template\html {

    /** @var \module\news\controller $module */
    public $module;

    public function get_view() {
        $html =
            node::create('div#article_wrapper.editable_content', [],
                node::create('article', [],
                    node::create('div.page-header', [], 
                        node::create('h1', [], $this->module->current->title) . 
                        node::create('span.author', [], 'By: ' . $this->module->current->poster)
                    ) .
                    $this->module->current->post
                ) .
                node::create('a.news_back.button', ['href' => '/news'], 'Back to news')
            );
        return $html;
    }
}
