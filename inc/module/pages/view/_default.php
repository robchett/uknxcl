<?php

class _default_view extends view {
    public function get_view() {
        return $this->module->current->body;
        return '';
    }

    public function get_page_selector() {
        return 'pages-' . $this->module->current->pid;
    }
}
