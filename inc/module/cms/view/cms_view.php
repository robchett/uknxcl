<?php

namespace module\cms\view;

use classes\get;
use module\cms\controller;
use template\cms\html;

abstract class cms_view extends html
{

    public function get(): string
    {
        return $this->get_view();
    }

    public function get_title_tag(): string
    {
        $parts = [parent::get_title_tag(), 'CMS', ucwords(str_replace('_', ' ', get::__class_name($this)))];
        if ($this->current) {
            $parts[] = ucwords(str_replace('_', ' ', $this->schema->table_name));
            if ($this->current->get_primary_key()) {
                if ($title = $this->current->get_title()) {
                    $parts[] = $title;
                } else {
                    $parts[] = $this->current->get_primary_key();
                }
            }
        }
        return implode(' | ', $parts);
    }
}
