<?php

namespace classes;

use form\field;
use html\node;

class attribute_callable implements \Stringable {

    public static function create(callable $arr): static {
        if (!is_array($arr)) throw new \Exception("Callable is not stringable");
        if (is_object($arr[0])) {
            $arr[0] = $arr[0]::class;
        }
        $class = new \ReflectionClass($arr[0]);
        $reflection = $class->getMethod($arr[1]);
        if (!$reflection->isStatic()) throw new \Exception("Callable is not static");
        return new static($arr);
    }

    public static function callback(callable $c): callable {
        return $c;
    }

    public function __construct(public $callable) {

    }

    public function __toString() {
        return "{$this->callable[0]}:{$this->callable[1]}";
    }
}