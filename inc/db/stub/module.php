<?php

namespace db\stub;

use Exception;

/**
 * Class module
 * Converts a json object into a usable class;
 * @package db\stub
 */
class module {

    public $dependencies;

    /** @var field[] */
    public array $fieldset = [];
    public $group;
    public $namespace;
    public $primary_key;
    public $tablename;
    public string $title;
    public bool $required = true;
    private string $mid;


    /**
     * @param $database
     * @return module
     * @throws Exception
     */
    public static function create($database): module {
        $file = root . '/inc/db/structures/' . $database . '.json';
        if (file_exists($file)) {
            $file = file_get_contents($file);
            $file = preg_replace_callback('#//@include \'(.*?)\'#', function ($matches) {
                $sub_file = root . '/inc/db/structures/' . $matches[1];
                return file_get_contents($sub_file);
            }, $file);
            $json = json_decode($file);
            if (is_object($json)) {
                if (!isset($json->fieldset)) {
                    $json->fieldset = [];
                }
                foreach ($json->fieldset as &$field) {
                    $field = field::create($field);
                }
                $module = new self;
                foreach ($json as $key => $value) {
                    $module->$key = $value;
                }
                return $module;
            } else {
                throw new Exception('Stub is not valid JSON: ' . $database);
            }
        } else {
            throw new Exception('Could not find table stub: ' . $database);
        }
    }

}
 