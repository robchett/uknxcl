<?php

namespace classes;

use arrayIterator;
use classes\collection as _collection;
use classes\db as _db;
use form\field_link;
use JetBrains\PhpStorm\Pure;

/**
 * Class table_array
 */
class table_array extends _collection {

    /* @var table_iterator */
    public arrayIterator $iterator;
    protected $fields;
    protected $options;
    protected array|string $class;

    public function __construct($input = [], $flags = 0, $iterator_class = \classes\collection_iterator::class) {
        parent::__construct($input, $flags, $iterator_class);
    }

    /**
     * @param $name
     * @return bool
     */
    #[Pure]
    public function has_field($name): bool {
        return in_array($name, $this->fields);
    }

    /**
     * @param $fields
     * @param $options
     */
    public function do_retrieve($fields, $options) {
        self::get_all($fields, $options);
    }

    /**
     * @param $class
     * @param array $fields_to_retrieve
     * @param array $options
     */
    public function get_all($class, array $fields_to_retrieve, $options = []) {
        $this->fields = $fields_to_retrieve;
        $this->options = $options;
        $this->class = $class;

        /** @var table $obj */
        $obj = new $class();
        $links = [];
        $mlinks = [];
        $obj->set_default_retrieve($fields_to_retrieve, $options);
        table::organise_links($obj, $fields_to_retrieve, $links, $mlinks);
        $dependencies = [get::__class_name($class)];
        foreach ($links as $module => $link_info) {
            $field = $link_info['field'];
            $retrieves = $link_info['retrieve'];
            $dependencies[] = get::__class_name($module);
            $options['join'][$module] = $module . '.' . $field->field_name . '=' . $obj->class_name() . '.' . $field->field_name;
            foreach ($retrieves as $retrieve) {
                $fields_to_retrieve[] = $module . '.' . $retrieve;
            }
        }
        $key = 'get_all_' . $class . '_fetch_' . implode(',', $fields_to_retrieve) . '_options_' . serialize($options);
        $elements = cache::grab($key,
            function () use ($class, $fields_to_retrieve, $options, $links, $mlinks, $obj) {
                if (!isset($options['order'])) {
                    $options['order'] = get::__class_name($class) . '.position';
                }
                $select = _db::get_query($class, $fields_to_retrieve, $options);
                $res = $select->execute();
                if (_db::num($res)) {
                    $row = _db::fetch($res);
                    $mappings = $obj->get_field_mappings(array_keys($row));
                    do {
                        /** @var table $class */
                        $object = new $class;
                        $object->set_from_row($row, $links, $mappings);
                        foreach ($mlinks as $module => $blah) {
                            $object->{$module . '_elements'} = new table_array();
                            $object->$module = new _collection();
                        }
                        $this[] = $object;
                    } while ($row = _db::fetch($res));
                }
                $this->reset_iterator();
                foreach ($mlinks as $module => $link_info) {
                    /** @var field_link $field */
                    $field = $link_info['field'];
                    $retrieves = $link_info['retrieve'];
                    $retrieves[] = 'l.' . $obj->get_primary_key_name() . ' AS linked_id';
                    $sub_class = $field->get_link_object();
                    $classes = $sub_class::get_all($retrieves, ['join' => [get::__class_name($class) . '_link_' . get::__class_name($sub_class) . ' l' => 'l.link_' . $sub_class->get_primary_key_name() . '=' . get::__class_name($sub_class) . '.' . $sub_class->get_primary_key_name()], 'where' => 'l.' . $obj->get_primary_key_name() . ' IN(' . implode(',', $this->get_table_keys()) . ')']);
                    /** @var table $sub_object */
                    foreach ($classes as $sub_object) {
                        $object = $this->find_table_key($sub_object->linked_id);
                        if ($object) {
                            $object->{$module . '_elements'}->push($sub_object);
                            $object->$module->push($sub_object->get_primary_key());
                        }
                    }
                }
                return $this->exchangeArray([]);
            }, $dependencies
        );
        $this->exchangeArray($elements);
    }

    protected function get_table_keys(): array {
        $res = [];
        $this->iterate(function ($object) use (&$res) {
            $res[] = $object->get_primary_key();
        }
        );
        return $res;
    }

    protected function find_table_key($id) {
        foreach ($this as $object) {
            if ($object->get_primary_key() == $id) {
                return $object;
            }
        }
        return false;
    }

    public function reduce(callable $callback, $intial = '') {
        return array_reduce($this->getArrayCopy(), $callback, $intial);
    }

    public function reverse() {
        $this->exchangeArray(array_reverse($this->getArrayCopy()));
    }

    /**
     * @return array|string
     */
    public function get_class(): array|string {
        return str_replace('_array', '', get_class($this));
    }

    /**
     *
     */
    public function rewind() {
        $this->iterator->rewind();
    }

    public function get_total_count() {
        if (isset($this->options['limit'])) {
            $options = $this->options;
            unset($options['limit']);
            $query = db::get_query($this->class, ['COUNT(*)'], $options)->execute()->fetch();
            return $query[0];
        }
        return $this->count();
    }
}