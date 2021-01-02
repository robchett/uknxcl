<?php

namespace module\news\model;

use classes\table;


class article extends table {


    /* @var string */
    public string $date;
    /* @var string */
    public string $poster;
    /* @var string */
    public string $post;
    /* @var int */
    public int $aid;
    public string $snippet;
    /* @var string */
    public string $title;


    public function get_url(): string {
        return '/news/' . $this->aid;
    }

    public function get_snippet(): string {
        return $this->snippet ?: $this->post;
    }
}
