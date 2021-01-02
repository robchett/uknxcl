<?php

namespace classes;

use form\field;
use html\node;

class attribute_list implements \ArrayAccess, \Stringable {

    public static function create(Array $arr): static {
        return new static($arr);
    }

    public static function callback(callable $c): callable {
        return $c;
    }

    public function __construct(public Array $attributes = []) {

    }

    public function offsetExists($offset) {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset) {
        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value) {
        return $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->attributes[$offset]);

    }

    public function __toString() {
        return node::get_attributes($this->attributes);
    }
}