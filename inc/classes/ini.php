<?php

namespace classes;


use Exception;

/**
 * Class ini
 *
 * Read/write controller for .ini files,
 *
 * Loads values from /.conf/config.ini
 * Merged with /.conf/$_SERVER["HTTP_HOST"].ini
 *
 * @package classes
 */
class ini {

    /** @var [] */
    private static $settings;

    /**
     * Get a  value from the conf files.
     *
     * @param $block
     * @param $key
     * @param null $default
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function get($block, $key, $default = null): mixed {
        if (!isset(self::$settings)) {
            self::load();
        }

        if (isset(self::$settings[$block][$key])) {
            return self::$settings[$block][$key];
        } else if (isset($default)) {
            return $default;
        } else {
            throw new Exception('Setting [' . $block . ']:' . $key . ' not found and no default provided');
        }
    }

    /**
     * Load the ini files
     * Loads values from /.conf/config.ini
     * Merged with /.conf/$_SERVER["HTTP_HOST"].ini
     *
     * @return void
     */
    public static function load() {
        self::$settings = parse_ini_file(root . '/.conf/config.ini', true);
    }

    /**
     * Get a block from the config file
     *
     * @param      $block
     * @param null $default
     *
     * @return mixed []
     * @throws Exception
     */
    public static function get_block($block, $default = null): array {
        if (!isset(self::$settings)) {
            self::load();
        }
        if (isset(self::$settings[$block])) {
            return self::$settings[$block];
        } else if (isset($default)) {
            return $default;
        } else {
            throw new Exception('ini block [' . $block . '] not found and no default provided');
        }
    }

    /**
     * Reload the config files
     * (Lazy: will be loaded on next call)
     *
     * @return void
     */
    public static function reload() {
        self::$settings = null;
    }

    /**
     * Modify and save an ini value
     * Only supports the default file
     *
     * @param $key
     * @param $value
     * @param string $block
     *
     * @return void
     */
    public static function modify($key, $value, string $block = 'site') {
        self::$settings[$block][$value] = $key;
        self::save(root . '/.conf/config.ini', self::$settings);
    }

    /**
     * Format and save and ini file with given options
     *
     * @param $file
     * @param $options
     *
     * @return void
     */
    public static function save($file, $options) {
        $string = '';
        foreach ($options as $block => $keys) {
            $string .= "[$block]\n";
            foreach ($keys as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $sub_value) {
                        $string .= $key . "[] = $sub_value\n";
                    }
                } else {
                    $string .= $key . " =$value\n";
                }
            }
            $string .= "\n";
        }
        file_put_contents($file, $string);
    }
}
 