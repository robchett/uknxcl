<?php

namespace classes\interfaces;

use classes\module;

/**
 * Class database_interface
 */
interface page_template_interface {
    public function __construct(module $module);

    public function get_page(): string;

    public function get_head(): string;

    public function get_title_tag();

    public function get_body(): string;

    public function get_footer(): string;

    public function get_view(): string;

    public function get_page_selector(): string;

    public function get(): array|string;
}
