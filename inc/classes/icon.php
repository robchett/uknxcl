<?php

namespace classes;

use html\node;

class icon {

    public static function get($icon, $tag = 'span', $attributes = []): string {
        $attr_string = node::get_attributes($attributes);
        return "<$tag class='glyphicon glyphicon-$icon' $attr_string></$tag>";
    }

}