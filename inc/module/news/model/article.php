<?php

namespace module\news\model;

use classes\table;
use classes\interfaces\model_interface;

class article  implements model_interface {
    use table;
    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position,
        public int $aid,
        public string $poster,
        public string $title,
        public int $date,
        public string $snippet,
        public string $post,
    )
    {
    }

    public function get_url(): string {
        return '/news/' . $this->aid;
    }

    public function get_snippet(): string {
        return $this->snippet ?: $this->post;
    }
}
