<?php

namespace classes\interfaces;

use classes\module;
use classes\push_state;

/**
 * Class database_interface
 */
interface page_template_interface {

    public function get_body_class(): string;

    /**
     * @param string[] $classes
     */
    public function add_body_class(...$classes): void;

    public function get_page(): string;

    public function get_head(): string;

    public function get_title_tag(): string;

    public function get_body(): string;

    public function get_footer(): string;

    public function get_view(): string;

    public function get_page_selector(): string;

    public function get(): string;

    public function get_push_state(): ?push_state;
}
