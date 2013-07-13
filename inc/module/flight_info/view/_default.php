<?php

class _default_view extends view {
    public function get_view() {
        return  $this->module->page_object->body;
    }
}
