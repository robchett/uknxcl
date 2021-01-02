<?php

namespace classes;

use JetBrains\PhpStorm\Pure;

class page_config {

    public string $pre_content = '';
    public string $post_content = '';
    public array $body_classes = [];
    private string $title_tag;

    #[Pure]
    public function get_body_class(): string {
        return implode('.', $this->body_classes);
    }

    public function add_body_class() {
        $classes = func_get_args();
        $this->body_classes = array_merge($this->body_classes, $classes);
    }
}
