<?php

namespace html\bootstrap;

use html\node;
use JetBrains\PhpStorm\Pure;

class modal {

    public string $title;
    public $body;
    public $footer;
    public $id;
    public array $attributes;

    /**
     * @param $id
     * @param array $attributes
     * @param null $title
     * @param null $body
     * @param null $footer
     */
    public function __construct($id, $attributes = [], $title = null, $body = null, $footer = null) {
        $this->title = $title;
        $this->footer = $footer;
        $this->body = $body;
        $this->id = $id;
        $this->attributes = $attributes;
    }

    /**
     * @param $id
     * @param array $attributes
     * @param null $title
     * @param null $body
     * @param null $footer
     *
     * @return modal
     */
    #[Pure]
    public static function create($id, array $attributes = [], $title = null, $body = null, $footer = null): modal {
        return new static($id, $attributes, $title, $body, $footer);
    }

    public function __toString(): string {
        return $this->get();
    }

    public function get(): string {
        return node::create('div#' . $this->id . '.modal.fade', $this->attributes, node::create('div.modal-dialog div.modal-content', [], node::create('div.modal-header', [], $this->title) . node::create('div.modal-body', [], $this->body) . node::create('div.modal-footer', [], $this->footer)));
    }

} 