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
            node::create('div#article_wrapper', [],
                node::create('article', [],
                    node::create('h2', [], $this->module->current->title) .
                    $this->module->current->post
                ) .
                node::create('a.news_back.button', ['href' => '/news'], 'Back to news')
            );
        return $html;
    }
}
