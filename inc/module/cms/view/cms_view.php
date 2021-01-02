<?php

namespace module\cms\view;

use classes\get;
use module\cms\controller;
use template\cms\html;

/**
 * Class cms_view
 *
 * @package cms
 * @property controller $module
 */
abstract class cms_view extends html {

    /**
     * @return string|array
     */
    public function get(): string|array {
        return (string)$this->get_view();
    }

    public function get_title_tag() {
        $parts = [parent::get_title_tag(), 'CMS', ucwords(str_replace('_', ' ', get::__class_name($this)))];
        if (isset($this->module->current)) {
            $parts[] = ucwords(str_replace('_', ' ', get::__class_name($this->module->current)));
            if ($this->module->current->get_primary_key()) {
                if ($title = $this->module->current->get_title()) {
                    $parts[] = $title;
                } else {
                    $parts[] = $this->module->current->get_primary_key();
                }
            }
        }
        return implode(' | ', $parts);
    }
}
