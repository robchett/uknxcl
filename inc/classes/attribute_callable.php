<?php

namespace classes;

use Exception;
use ReflectionClass;
use Stringable;

final class attribute_callable implements Stringable {

    public static function create(callable $arr): self {
        if (!is_array($arr)) throw new Exception("Callable is not stringable");
        if (is_object($arr[0])) {
            $arr[0] = $arr[0]::class;
        }
        $class = new ReflectionClass($arr[0]);
        $reflection = $class->getMethod($arr[1]);
        if (!$reflection->isStatic()) throw new Exception("Callable is not static");
        return new self($arr);
    }

    public static function callback(callable $c): callable {
        return $c;
    }

    /** @param array{class-string, string} $callable */
    public function __construct(
        public array $callable
    ) {

    }

    public function __toString() {
        return "{$this->callable[0]}:{$this->callable[1]}";
    }
}