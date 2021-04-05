<?php

namespace classes;

class auto_loader {

    protected static array $file_paths = [];

    public function __construct() {
        spl_autoload_register(['self', 'load']);
        $this->load_cache();
    }

    public function load_cache(): bool {
        static::$file_paths = cache::get('autoloader.file_paths', ['autoloader'])->getResult([]);
        return true;
    }

    public function load($class): bool {
        static $depth = 0;
        $depth++;
        if (isset(static::$file_paths[$class])) {
            $path = static::$file_paths[$class];
        } else {

            $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $path = root . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . $class_path . '.php';
            static::$file_paths[$class] = $path;
        }

        if (file_exists($path)) {
            require_once($path);
            if ($depth == 1) {
                if (method_exists($class, 'set_statics')) {
                    $class::set_statics();
                }
            }
            $depth--;
            return true;
        } else {
            $depth--;
            return false;
        }
    }

    public function __destruct() {
        cache::set(['autoloader.file_paths' => static::$file_paths], ['autoloader']);
    }
}
