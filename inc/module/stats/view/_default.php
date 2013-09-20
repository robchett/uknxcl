<?php

class _default_view extends view {

    /** @return html\node */
    public function get_view() {
        return $this->module->page_object->body;
    }
}
