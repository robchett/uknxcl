<?php

namespace classes;

use Exception;
use InvalidArgumentException;

class session {

    protected static bool $started = false;
    protected static bool $modified = false;

    public static function stop(): void {
        if (static::$started) {
            session_write_close();
        }
    }

    /**
     * @param string[] $path
     * @return mixed
     */
    public static function get(...$path): mixed {
        static::start();
        $var = $_SESSION;
        foreach ($path as $key) {
            if (!isset($var[$key])) {
                throw new InvalidArgumentException('$_SESSION[' . implode('][', $path) . '] has not been set');
            }
            /** @var array|scalar */
            $var = $var[$key];
        }
        return $var;
    }

    protected static function start(): void {
        if (!static::$started) {
            session_start();
            static::$started = true;
        }
    }

    /**
     * @param string[] $path
     * @return bool
     */
    public static function is_set(...$path): bool {
        static::start();
        $var = $_SESSION;
        foreach ($path as $key) {
            if (!isset($var[$key])) {
                return false;
            }
            /** @var array|scalar */
            $var = $var[$key];
        }
        return true;
    }

    /**
     * @param string[] $path
     * @return void
     */
    public static function un_set(...$path): void {
        static::start();
        static::$modified = true;
        $final = array_pop($path);
        $var = &$_SESSION;
        foreach ($path as $key) {
            if (!isset($var[$key])) {
                return;
            }
            /** @var array */
            $var = &$var[$key];
        }
        unset($var[$final]);
    }

    /**
     * @param mixed $value
     * @param string[] $input
     * @return void
     */
    public static function set(mixed $value, ...$input): void {
        static::start();
        static::$modified = true;
        $final = array_pop($input);
        $var = &$_SESSION;
        foreach ($input as $key) {
            if (isset($var[$key]) && !is_array($var[$key])) {
                throw new Exception('Session variable overwritten with path');
            }
            /** @psalm-suppress PossiblyInvalidArrayAssignment, MixedAssignment */
            $var[$key] ??= [];
            /** @var array|scalar */
            $var = &$var[$key];
        }
        /** @psalm-suppress PossiblyInvalidArrayAssignment, MixedAssignment */
        $var[$final] = $value;
    }
}
 