<?php

namespace classes;

use Exception;

class error_handler {
    // context version
    const VERSION = '1.1.0';

    // redis configuration
    const DEBUG = 1;
    const INFO = 2;
    const NOTICE = 4;      // NB will also use <$channel>_seq

    // syslog facilities
    const WARNING = 8;
    const ERROR = 16;
    const CRITICAL = 32;
    const ALERT = 64;
    const EMERGENCY = 128;
    const LOG_STATEMENT = 1;
    const ERROR_HANDLER = 2;
    const EXCEPTION_HANDLER = 3;

    // data source
    public static string $server = 'localhost:6379';
    public static int $db = 1;
    public static string $channel = 'L8';

    // internals
    protected static $socket;
    protected static $domain;


    public static function debug($message, array $context = []): bool {
        return static::log(static::DEBUG, $message, $context);
    }

    protected static function log($level, $message, array $context = []): bool {
        if (count($trace = debug_backtrace(false, 2)) > 1) {
            $file = $trace[1]['file'];
            $line = $trace[1]['line'];
        } else {
            $file = '';
            $line = 0;
        }

        return static::write($level, static::LOG_STATEMENT, $message, $file,
            $line, $context);
    }

    protected static function write($level, $source, $message, $file, $line,
                                    $context): bool {
        // connect to redis server
        if (!isset(static::$socket)) {
            static::$socket = stream_socket_client(static::$server);
            if (static::$socket) {
                static::select(static::$db);

                static::$domain = array_key_exists('SERVER_NAME', $_SERVER)
                    ? strtolower($_SERVER['SERVER_NAME'])
                    : null;
            }
        }

        // make sure the socket was opened
        if (!static::$socket) {
            return false;
        }

        // generate the atomic log entry id #
        $id = static::incr(self::$channel . '.seq');

        // construct the data record
        $record = json_encode(
            [
                'version'  => static::VERSION,
                'id'       => $id,
                'domain'   => static::$domain,
                'time'     => time(),
                'level'    => $level,
                'source'   => $source,
                'message'  => $message,
                'filename' => $file,
                'line'     => $line,
                'context'  => base64_encode(json_encode($context)),
            ]
        );

        static::publish(self::$channel, $record);

        return true;
    }

    protected static function select($index) {
        return static::execute(['SELECT', $index]);
    }

    protected static function execute($args) {
        $cmd = '*' . count($args) . "\r\n";
        foreach ($args as $arg) {
            $cmd .= '$' . strlen($arg) . "\r\n" . $arg . "\r\n";
        }

        fwrite(static::$socket, $cmd);

        return static::parseResponse();
    }

    protected static function parseResponse() {
        $line = fgets(static::$socket);

        [$type, $result] = [
            $line[0],
            substr($line, 1,
                strlen($line) - 3),
        ];

        if ($type == '-') {
            throw new Exception($result);
        } else if ($type == '$') {
            if ($result == -1) {
                $result = null;
            } else {
                $line = fread(static::$socket, $result + 2);
                $result = substr($line, 0, strlen($line) - 2);
            }
        } else if ($type == '*') {
            $count = (int)$result;

            for ($i = 0, $result = []; $i < $count; $i++) {
                $result[] = static::parseResponse();
            }
        }

        return $result;
    }

    protected static function incr($key) {
        return static::execute(['INCR', $key]);
    }

    protected static function publish($channel, $message) {
        return static::execute(['PUBLISH', $channel, $message]);
    }

    public static function info($message, array $context = []): bool {
        return static::log(static::INFO, $message, $context);
    }

    public static function notice($message, array $context = []): bool {
        return static::log(static::NOTICE, $message, $context);
    }

    public static function warning($message, array $context = []): bool {
        return static::log(static::WARNING, $message, $context);
    }

    public static function error($message, array $context = []): bool {
        return static::log(static::ERROR, $message, $context);
    }

    public static function critical($message, array $context = []): bool {
        return static::log(static::CRITICAL, $message, $context);
    }

    public static function alert($message, array $context = []): bool {
        return static::log(static::ALERT, $message, $context);
    }

    public static function emergency($message, array $context = []): bool {
        return static::log(static::EMERGENCY, $message, $context);
    }

    public static function fatal_handler() {
        $error = error_get_last();
        if ($error !== null) {
            $errno = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr = $error["message"];
            static::handle_error($errno, $errstr, $errfile, $errline);
        }
    }

    public static function handle_error($errno, $message, $file = '', $line = 0, $context = []): bool {
        if (!is_array($context)) {
            $context = [$context];
        }

        $map = [
            E_ERROR             => ['E_ERROR', static::ERROR],
            E_WARNING           => ['E_WARNING', static::WARNING],
            E_PARSE             => ['E_PARSE', static::ERROR],
            E_NOTICE            => ['E_NOTICE', static::NOTICE],
            E_CORE_ERROR        => ['E_CORE_ERROR', static::ERROR],
            E_CORE_WARNING      => ['E_CORE_WARNING', static::WARNING],
            E_COMPILE_ERROR     => ['E_COMPILE_ERROR', static::ERROR],
            E_COMPILE_WARNING   => ['E_COMPILE_WARNING', static::WARNING],
            E_USER_ERROR        => ['E_USER_ERROR', static::ERROR],
            E_USER_WARNING      => ['E_USER_WARNING', static::WARNING],
            E_USER_NOTICE       => ['E_USER_NOTICE', static::NOTICE],
            E_STRICT            => ['E_STRICT', static::WARNING],
            E_RECOVERABLE_ERROR => ['E_RECOVERABLE_ERROR', static::WARNING],
            E_DEPRECATED        => ['E_DEPRECATED', static::WARNING],
            E_USER_DEPRECATED   => ['E_USER_DEPRECATED', static::WARNING],
        ];

        [$prefix, $level] = array_key_exists($errno, $map)
            ? $map[$errno]
            : ['E_UNKOWN(' . $errno . ')', static::ERROR];

        return static::write($level, static::ERROR_HANDLER, $prefix . ':' .
            $message, $file, $line, $context);
    }

    public static function exception_handler(Exception $e): bool {
        return static::write(static::ERROR, static::EXCEPTION_HANDLER,
            $e->getMessage(),
            $e->getFile(), $e->getLine(), []);
    }

    protected static function set($key, $value) {
        return static::execute(['SET', $key, $value]);
    }
}
