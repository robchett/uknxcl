<?php
namespace module\news\view;

use module\news\object\article;
use traits\twig_view;

class _default extends \template\html {
    use twig_view;

    function get_template_data() {
        $articles = article::get_all(['aid', 'date', 'title', 'poster', 'post', 'snippet',], ['order' => 'date DESC', 'where' => 'parent_aid=0']);

        return [
            'articles' => $articles->get_template_data()
        ];
    }
}
