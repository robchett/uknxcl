<?php

namespace classes;

use html\node;

class icon {

    /** @param array{class?: string[]} $attributes */
    public static function get(string $icon, string $tag = 'span', array $attributes = []): string {
        $attributes['class'] ??= [];
        $attributes['class'][] = 'glyphicon';
        $attributes['class'][] = 'glyphicon-' . $icon;
        $attr_string = node::get_attributes($attributes);
        return "<$tag $attr_string></$tag>";
    }

}