<?php

namespace classes;

use Exception;
use Memcached;

class cache implements interfaces\cache_interface {

    const ERROR_CONNECT = 1;
    const DEFAULT_CACHE_TIME = 86400;
    public static ?Memcached $current = null;
    /** @var string[] */
    protected static array $ignore_tables = ['_cache_dependants', '_compiler_keys'];
    /** @var string[] */
    private static ?array $dependants;

    /**
     * @param string $instance_id
     * @param string $server
     * @param int $port
     * @return bool
     * @throws Exception
     */
    public static function connect(string $instance_id = '', string $server = 'localhost', int $port = 11211): bool {
        if (class_exists('Memcached', false)) {
            $cache = new Memcached($instance_id);
            $cache->addServer($server, $port);
            self::$current = $cache;
        } else {
            throw new Exception('Memcached is not enabled on this server.', self::ERROR_CONNECT);
        }
        return true;
    }

    /**
     * Flush the current cache pool
     */
    public static function flush(): bool {
        return (bool) self::$current->flush();
    }

    /**
     * @template T
     * @param callable(): T $callback
     * @param string[] $dependencies
     * @return T
     */
    public static function grab(string $key, callable $callback, array $dependencies = ['global'], ?int $time = null) {
        $cacheResult = cache::get($key, $dependencies);
        if (!$cacheResult->success) {
            $data = $callback();
            cache::set([$key => $data], $dependencies, $time);
            return $data;
        }
        return $cacheResult->result;
    }

    /**
     * @param string $key the key to retrieve.
     * @param string[] $dependencies table dependencies.
     * @return cacheResult
     */
    public static function get(string $key, array $dependencies = ['global']): cacheResult {
        if (self::$current == null) {
            return new cacheResult(false, false, 'Not connected');
        }
        $key = self::get_key($key, $dependencies);
        $res = self::$current->get($key);
        $code = self::$current->getResultCode();
        $ok = $code == Memcached::RES_SUCCESS;
        return new cacheResult($ok, $res, $code);
    }

    /**
     * @param string[] $dependencies
     */
    protected static function get_key(string $key, array $dependencies): string {
        self::$dependants ??= self::load_dependants();
        $salt = '';
        foreach ($dependencies as $dependant) {
            if (!isset(self::$dependants[$dependant])) {
                self::break_cache($dependant);
            }
            $salt .= self::$dependants[$dependant] ?? 0;
        }
        return md5($salt . $key);
    }

    /**
     * Load the table dependencies for dynamic cache breaking.
     * @return string[]
     */
    private static function load_dependants(): array {
        $dependants = [];
        if (class_exists('\classes\db')) {
            $res = db::query('SELECT * FROM _cache_dependants');
            while ($row = db::fetch($res)) {
                $dependants[$row['key']] = $row['hash'];
            }
        }
        return $dependants;
    }

    public static function break_cache(string $table): void {
        if (in_array($table, static::$ignore_tables)) {
            return;
        }
        $time = microtime(true);
        db::replace('_cache_dependants')->add_value('key', $table)->add_value('hash', $time)->execute();
        self::$dependants[$table] = $time;
    }

    /**
     * @param array<string, mixed> $data associative array of key => value to be added the the cache table.
     * @param string[] $dependencies table dependencies.
     * @param ?int $cache_time Cache time in seconds, 0 for not breaking
     * @return bool returns true on successful add or false on failure.
     */
    public static function set(array $data, array $dependencies = ['global'], ?int $cache_time = null): bool {
        if (self::$current == null) {
            return false;
        }
        if (is_null($cache_time)) {
            $cache_time = self::DEFAULT_CACHE_TIME;
        }
        $ok = 1;
        foreach ($data as $key => $value) {
            $new_key = self::get_key($key, $dependencies);
            $ok &= self::$current->set($new_key, $value, $cache_time);
        }
        return (bool) $ok;
    }
}
