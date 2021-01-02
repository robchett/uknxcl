<?php

namespace classes;

class cacheResult {

    public function __construct(public bool $success, public mixed $result, public mixed $reason) {
    }

    public function getResult(mixed $default): mixed {
        return $this->success ? $this->result : $default;
    }
}