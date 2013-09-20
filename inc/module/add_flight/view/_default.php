<?php
namespace add_flight;
class _default_view extends \view {
    public function get_view() {
        $html = $this->module->page_object->body;
        return $html;
    }
}
