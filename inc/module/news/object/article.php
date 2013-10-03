<?php
namespace module\news\object;

use core\classes\table;
use html\node;
use traits\table_trait;

class article extends table {

    use table_trait;

    /* @var string */
    public $date;
    /* @var string */
    public $poster;
    /* @var string */
    public $post;
    /* @var int */
    public $aid;
    public $snippet;
    /* @var string */
    public $title;

    public $table_key = 'aid';
    public static $module_id = 8;


    public function get_url() {
        return '/news/' . $this->aid;
    }

    public function get_title() {
        return
            node::create('span.date', [], date('d/m/Y', strtotime($this->date))) .
            node::create('span.name', [], $this->title) .
            node::create('span.author', [], 'By: ' . $this->poster);
    }

    public function get_body() {

    }

    public function get_cell() {
        return node::create('article#article' . $this->aid, [],
            node::create('div.title', [], $this->get_title()) .
            node::create('div.content', [], (!$this->snippet ? $this->post : $this->snippet . node::create('a.button', ['href' => $this->get_url()], 'Read more')))
        );
    }
}
