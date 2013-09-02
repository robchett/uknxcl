<?php

class _default_view extends view {

    /** @return html_node */
    public function get_view() {
        return $this->module->page->body;
    }
}
