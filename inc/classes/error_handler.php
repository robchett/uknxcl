<?php

namespace classes;

use Exception;

final class error_handler
{
    // context version
    const VERSION = '1.1.0';

    // redis configuration
    const DEBUG = 1;
    const INFO = 2;
    const NOTICE = 4;
    const WARNING = 8;
    const ERROR = 16;
    const CRITICAL = 32;
    const ALERT = 64;
    const EMERGENCY = 128;

    const LOG_STATEMENT = 1;
    const ERROR_HANDLER = 2;
    const EXCEPTION_HANDLER = 3;

    private static array $levels = [
        self::DEBUG => 'E_DEBUG',
        self::INFO => 'E_INFO',
        self::NOTICE => 'E_NOTICE',
        self::WARNING => 'E_WARNING',
        self::ERROR => 'E_ERROR',
        self::CRITICAL => 'E_CRITICAL',
        self::ALERT => 'E_ALERT',
        self::EMERGENCY => 'E_EMERGENCY',
    ];

    // internals
    protected static string $domain = '?';


    public static function debug(string $message, array $context = []): bool
    {
        return static::log(self::DEBUG, $message, $context);
    }

    protected static function log(int $level, string $message, array $context = []): bool
    {
        if (count($trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)) > 1) {
            $file = $trace[1]['file'];
            $line = $trace[1]['line'];
        } else {
            $file = '';
            $line = 0;
        }

        return static::write($level, static::LOG_STATEMENT, $message, $file, $line, $context);
    }

    protected static function write(int $level, int $source, string $message, string $file, int $line, array $context): bool
    {
        /** @var ?string $id */
        static $id;
        $id ??= md5(microtime());

        // construct the data record
        $record = [
            'version'  => static::VERSION,
            'id'       => $id,
            'domain'   => static::$domain,
            'time'     => time(),
            'level'    => $level,
            'source'   => $source,
            'message'  => $message,
            'filename' => "$file:$line",
            'context'  => $context,
        ];
        error_log(json_encode($record), JSON_UNESCAPED_SLASHES);
        return true;
    }

    public static function info(string $message, array $context = []): bool
    {
        return static::log(static::INFO, $message, $context);
    }

    public static function notice(string $message, array $context = []): bool
    {
        return static::log(static::NOTICE, $message, $context);
    }

    public static function warning(string $message, array $context = []): bool
    {
        return static::log(static::WARNING, $message, $context);
    }

    public static function error(string $message, array $context = []): bool
    {
        return static::log(static::ERROR, $message, $context);
    }

    public static function critical(string $message, array $context = []): bool
    {
        return static::log(static::CRITICAL, $message, $context);
    }

    public static function alert(string $message, array $context = []): bool
    {
        return static::log(static::ALERT, $message, $context);
    }

    public static function emergency(string $message, array $context = []): bool
    {
        return static::log(static::EMERGENCY, $message, $context);
    }

    public static function fatal_handler(): void
    {
        $error = error_get_last();
        if ($error !== null) {
            $errno = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr = $error["message"];
            static::handle_error($errno, $errstr, $errfile, $errline);
        }
    }

    public static function handle_error(int $errno, string $message, string $file = '', int $line = 0, array $context = []): bool
    {
        function_exists('xdebug_break') && \xdebug_break();
        $level = match ($errno) {
            E_NOTICE, E_USER_NOTICE                                                                                                      => static::NOTICE,
            E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR                                                                => static::ERROR,
            E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING, E_STRICT, E_RECOVERABLE_ERROR, E_DEPRECATED, E_USER_DEPRECATED => static::WARNING,
        };
        return static::write($level, static::ERROR_HANDLER, $message, $file, $line, $context);
    }

    public static function exception_handler(\Throwable $e): bool
    {
        function_exists('xdebug_break') && \xdebug_break();
        return static::write(static::ERROR, static::EXCEPTION_HANDLER, $e->getMessage(), $e->getFile(), $e->getLine(), []);
    }
}
