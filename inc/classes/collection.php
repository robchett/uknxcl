<?php

namespace classes;

use arrayIterator;
use ArrayObject;

/**
 * @psalm-template T
 * @extends ArrayObject<array-key, T>
 */
class collection extends ArrayObject {

    /**
     * @param array<int, T> $input
     * @param int $flags
     */
    public function __construct($input = [], $flags = 0) {
        parent::__construct($input, $flags);
    }

    /**
     * @param callable(T, int): string $function
     */
    public function iterate_return(callable $function, int &$cnt = 0): string {
        $res = '';
        $cnt = 0;
        /** @var array<int, T> */
        $arr = $this->exchangeArray([]);
        foreach ($arr as $object) {
            $res .= call_user_func_array($function, [$object, $cnt++]);
        }
        $this->exchangeArray($arr);
        return $res;
    }


    /**
     * @template S
     * @param callable(S, T): S $callback
     * @param S $intial
     * @return S
     * @psalm-suppress MixedInferredReturnType
     */
    public function reduce(callable $callback, mixed $intial): mixed {
        /** @psalm-suppress MixedReturnStatement */
        return array_reduce($this->getArrayCopy(), $callback, $intial);
    }

    /**
     * @return T
     */
    public function last(): mixed {
        /** @var array<int, T> */
        $arr = $this->exchangeArray([]);
        $res = end($arr);
        $this->exchangeArray($arr);
        return $res;
    }

    /**
     * @param callable(T, int): void $function
     */
    public function iterate(callable $function, int &$cnt = 0): void {
        foreach ($this as $object) {
            call_user_func_array($function, [$object, $cnt]);
            $cnt++;
        }
    }
}
 