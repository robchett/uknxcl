<?php

namespace classes\interfaces;

use classes\cacheResult;
use Exception;

interface cache_interface {

    /**
     * @param string $key the key to retrieve.
     * @param string[] $dependencies table dependencies.
     * @return cacheResult
     * @throws Exception Throws \Exceptions if the cache node could not be connected or the key is not set.
     */
    public static function get(string $key, array $dependencies = ['global']): cacheResult;

    /**
     * @param array<string, mixed> $data associative array of key => value to be added the the cache table.
     * @param string[] $dependencies table dependencies.
     * @return bool
     */
    public static function set(array $data, array $dependencies = ['global']): bool;

    public static function connect(string $instance_id = ''): bool;

    public static function flush(): bool;
}
