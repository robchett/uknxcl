<?php

namespace classes;

use html\node;

class icon {

    public static function get($icon, $tag = 'span', $attributes = []): string {
        $attributes['class'][] = 'glyphicon';
        $attributes['class'][] = 'glyphicon-' . $icon;
        $attr_string = node::get_attributes($attributes);
        return "<$tag $attr_string></$tag>";
    }

}