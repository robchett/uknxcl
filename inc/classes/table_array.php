<?php

namespace classes;

use arrayIterator;
use classes\collection as _collection;
use classes\db as _db;
use classes\interfaces\model_interface;
use form\field_link;
use form\schema;

/**
 * @template T of model_interface
 * @extends _collection<T>
 */
class table_array extends _collection {

    protected tableOptions $options;
    protected string $class;

    /**
     * @param array<int, T> $input
     * @param int $flags
     * @param class-string $iterator_class
     */
    public function __construct($input = [], $flags = 0) {
        parent::__construct($input, $flags);
    }

    /**
     * @template S of model_interface
     * @param class-string<S> $class
     * @param string[] $fields_to_retrieve
     * @return self<S>
     * @psalm-suppress InvalidReturnType
     */
    public static function get_all(string $class, tableOptions $options): self {
        $list = new self;
        $list->options = $options;
        $list->class = $class;
        $module = schema::getFromClass($class);
        [$res, $aliases] = $module->getFetchQuery($options);
        $rows = [];
        while ($row = db::fetch($res)) {
            $rows[] = $module->processRow($row, $aliases);
        }
        $list->exchangeArray($rows);
        /** @psalm-suppress InvalidReturnStatement */
        return $list;
    }

    /**
     * @template S
     * @param callable(T): S $callback
     * @return S[]
     */
    public function map(callable $callback): array {
        return array_map($callback, $this->getArrayCopy());
    }

    public function reverse(): void {
        $this->exchangeArray(array_reverse($this->getArrayCopy()));
    }

    /**
     * @return array|string
     */
    public function get_class(): array|string {
        return str_replace('_array', '', get_class($this));
    }

    public function get_total_count():int {
        if ($this->options->limit) {
            $options = clone $this->options;
            $module = schema::getFromClass($this->class);
            $options->limit = '';
            /** @var array{int} */
            $query = db::get_query($module->table_name, ['COUNT(*)'], $options)->execute()->fetch();
            return $query[0];
        }
        return $this->count();
    }
}