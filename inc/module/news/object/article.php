<?php
namespace module\news\object;

use classes\table;
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



    public function get_url() {
        return '/news/' . $this->aid;
    }
}
