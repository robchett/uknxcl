<?php

namespace form;

use classes\collection;
use classes\db;
use classes\get;
use classes\interfaces\model_interface;
use classes\table;
use classes\table_array;
use classes\tableOptions;
use Exception;
use PDOStatement;

class schema
{
    /** @var self[] cms_modules */
    protected static array $cms_modules;
    /** @var array<string, array<string, string>> */
    protected static array $groups;

    /**
     * @param class-string<model_interface> $object
     * @param array<string, field> $fields
     */
    public function __construct(
        public string $primary_key,
        public string $namespace,
        public string $table_name,
        public string $object,
        public bool $nestable = false,
        public array $fields = [],
    ) {
    }

        /**
     * @param schema[] $schemas
     * @param array<string, array<string, string>> $groups
     */
    public static function setSchema(array $schemas, array $groups): void {
        static::$cms_modules = $schemas;
        static::$groups = $groups;
        // foreach ($schemas as $schema) {
        //     $schema->object::get(new tableOptions());
        // }
    }

    /** @return self[] */
    public static function getSchemas(): array {
        return self::$cms_modules;
    }

    
    /** @return array<string, array<string, string>> */
    public static function getGroups(): array {
        return self::$groups;
    }

    public static function getFromClass(string $class): self {
        if (isset(self::$cms_modules[$class])) {
            return self::$cms_modules[$class];
        }
        foreach (self::$cms_modules as $module) {
            if ($module->object == $class) {
                return $module;
            }
        }
        throw new Exception('Module not found: ' . $class);
    }

        /**
     * @param string[] $fields
     * @return array{string[], tableOptions}
     */
    public function set_default_retrieve(tableOptions $options, ?string $alias = null): array {
        $alias ??= $this->table_name;
        $fields = ['live', 'deleted', 'position', 'created', 'ts'];
        foreach($this->fields as $field) {
            if ($field instanceof field_button) { continue; }
            $fields[] = $field->field_name;
            if ($field instanceof field_link) {
                $schema = self::getFromClass($field->link_module);
                $join_name = "{$alias}__$schema->table_name";
                $options->join["{$schema->table_name} $join_name"] = "{$alias}.{$field->field_name} = {$join_name}.{$schema->primary_key}";
                // $options->where_equals["$join_name.live"] = 1;
                // $options->where_equals["$join_name.deleted"] = 1;
                [$subfields, $options] = $schema->set_default_retrieve($options, $join_name);
                $fields = array_merge($fields, array_map(fn(string $f) => "{$join_name}.{$f}", $subfields));
            }
        }
        return [$fields, $options];
    }

    public function getField(string $fieldname): field|false {
        foreach ($this->fields as $field) {
            if ($field->field_name == $fieldname) {
                return $field;
            }
        }
        return false;
    }

    /**
     * @return array{PDOStatement, array<string, string>}
     */
    public function getFetchQuery(tableOptions $options): array {
        [$rawFields, $options] = $this->set_default_retrieve($options); 
        if (!$options->retrieve_deleted) {
            $options->where_equals[$this->table_name . '.deleted'] = 0;
        }
        if (!$options->retrieve_unlive) {
            $options->where_equals[$this->table_name . '.live'] = 1;
        }
        $fields = $aliases = [];
        foreach ($rawFields as $f) {
            if (str_contains($f, '.')) {
                // Store an alias of any fields that exceed 256 chars
                $source = strrev(explode('.', strrev($f), )[1]) . '.' . strrev(explode('.', strrev($f), )[0]);
                $alias = str_replace('.', '@', $f);
                $alias_alias = strlen($f) > 64 ? md5($f) : $f;            
                $aliases[$alias] = $alias_alias;
                $fields[] = "$source `$alias_alias`";
            } else {
                $aliases[$f] = $f;
                $fields[] = "{$this->table_name}.$f `$f`";
            }
        }
        $query = db::get_query($this->table_name, $fields, $options);
        return [$query->execute(), $aliases];
    }

    /**
     * @param array<string, string|null> $row
     * @param array<string, string> $aliases
     */
    public function processRow(array $row, array $aliases): model_interface {
        /** @psalm-suppress PossiblyNullArgument */
        $out = [
            'live' => (bool) $row[$aliases['live']],
            'deleted' => (bool) $row[$aliases['deleted']],
            'position' => (int) $row[$aliases['position']],
            'ts' => $row[$aliases['ts']] ? strtotime($row[$aliases['ts']]) : 0,
            'created' => $row[$aliases['created']] ? strtotime($row[$aliases['created']]) : 0,
        ];
        $submodule_out = [];
        foreach ($this->fields as $key => $field) {
            if ($field instanceof field_button) { continue; }
            /** @psalm-suppress MixedAssignment, PossiblyNullArgument */
            $out[$key] = $row[$key] !== null ? $field::sanitise_from_db($row[$aliases[$key]]) : $field->default;
        }       
        $alias_flip = array_flip($aliases); 
        foreach ($row as $key => $val) {
            $key = $alias_flip[$key];
            if (strstr($key, '@')) {
                [$module, $field] = explode('@', $key, 2);
                $trimmed_module = explode('__', $module, 2)[1];
                $trimmed_field = str_contains($field, '@') ? substr($field, strlen($this->table_name) + 2) : $field;
                $submodule_out[$trimmed_module] ??= ['row' => [], 'alias' => []];
                $submodule_out[$trimmed_module]['row'][$trimmed_field] = $val;
                $submodule_out[$trimmed_module]['alias'][$trimmed_field] = $trimmed_field;
            }
        }
        foreach ($submodule_out as $submodule => $subrow) {
            foreach ($this->fields as $field) {
                if ($field instanceof field_link && $submodule == get::__basename($field->link_module)) {
                    $module = self::getFromClass($field->link_module);
                    $out[$submodule] = $module->processRow($subrow['row'], $subrow['alias']);
                    break;
                }
            }
        }
        $res = new ($this->object)(...$out); 
        return $res;
    }
}
