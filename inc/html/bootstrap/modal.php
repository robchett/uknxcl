<?php

namespace html\bootstrap;

use classes\attribute_list;
use html\node;

final class modal
{

    public function __construct(
        public string $id,
        public attribute_list $attributes,
        public string $title = '',
        public string $body = '',
        public string $footer = ''
    ) {
    }

    public static function create(string $id, attribute_list $attributes, string $title = '', string $body = '', string $footer = ''): self
    {
        return new self($id, $attributes, $title, $body, $footer);
    }

    public function __toString(): string
    {
        return $this->get();
    }

    public function get(): string
    {
        return "
        <div id='{$this->id}' class='modal fade' {$this->attributes}>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>{$this->title}</div>
                    <div class='modal-body'>{$this->body}</div>
                    <div class='modal-footer'>{$this->footer}</div>
                </div>
            </div>
        </div>";
    }
}
