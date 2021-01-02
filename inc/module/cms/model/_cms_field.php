<?php

namespace module\cms\model;

use classes\table;
use Exception;
use form\field;
use module\cms\model\_cms_module as __cms_module;


class _cms_field extends table {


    public static array $default_fields = ['fid', 'parent_fid', 'field_name', 'title', 'type', 'mid', 'list', 'filter', 'required', 'link_module', 'link_field'];
    /**
     * @var static[]
     */
    protected static array $cms_fields;
    public int $fid;
    public string $field_name;
    public string $title;
    public string $type;
    public int $link_field;
    public int $link_module;
    public string $primary_key = 'fid';
    public bool $required;
    public bool $filter;
    public bool $list;

    public static function create($field_name, $structure, $module): static {
        $field = new static();
        $field->do_retrieve([], ['where_equals' => ['mid' => $module, 'field_name' => $field_name]]);
        if (!$field->get_primary_key()) {
            $field->field_name = $field_name;
            $field->mid = $module;
            $field->type = $structure->type;
            $field->title = isset($structure->title) ? $structure->title : ucwords(str_replace('_', ' ', $field_name));
            if (isset($structure->module) && $structure->module) {
                $_module = new __cms_module();
                $_module->do_retrieve(['mid'], ['where_equals' => ['table_name' => $structure->module]]);
                $field->link_module = $_module->mid;
                if (isset($structure->field) && $structure->field) {
                    $_field = new static();
                    $_field->do_retrieve(['fid'], ['where_equals' => ['field_name' => $structure->field, 'mid' => $_module->mid]]);
                    if ($_field->get_primary_key()) {
                        $field->link_field = $_field->fid;
                    }
                }
            }
            $field->list = (isset($structure->list) ? $structure->list : true);
            $field->filter = (isset($structure->filter) ? $structure->filter : true);
            $field->required = (isset($structure->required) ? $structure->required : true);
            $field->do_save();
        } else {
            throw new Exception('Field ' . $field_name . ' already exists in module ' . $module);
        }
        return $field;
    }

    /**
     * @param $fid
     * @return ?static
     */
    public static function get_field_from_fid($fid): ?_cms_field {
        if (!isset(self::$cms_fields)) {
            $cms_fields = static::get_all([]);
            $cms_fields->iterate(function ($object) {
                self::$cms_fields[$object->fid] = $object;
            });
        }
        return self::$cms_fields[$fid] ?? null;
    }

    public function get_field(): field {
        $class = '\\form\\field_' . $this->type;
        /** @var field $field */
        $field = new $class($this->field_name, []);
        $field->set_from_row($this);
        return $field;
    }

    public function get_primary_key_name(): string {
        return 'fid';
    }
}
