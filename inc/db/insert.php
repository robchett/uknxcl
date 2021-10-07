<?php

namespace db;

use classes\cache;
use classes\db as _db;
use db\query as _query;

class insert extends _query {

    /** @var array<string, scalar> */
    protected array $values = [];
    protected string $mode;

    public function __construct(string $table, string $mode = '') {
        parent::__construct($table);
        $this->mode = $mode;
    }

    public function execute(): string {
        $query = 'INSERT ' . $this->mode . ' INTO ' . $this->table . ' SET ' . $this->get_values();
        _db::query($query, $this->parameters);
        $id = _db::insert_id();
        cache::break_cache($this->table);
        return $id;
    }

    protected function get_values(): string {
        $sql = [];
        foreach ($this->values as $field => $value) {
            $sql[] = '`' . $field . '` = :' . $field;
            $this->parameters[$field] = $value;
        }
        return implode(', ', $sql);
    }

    /** @param scalar $value */
    public function add_value(string $field, mixed $value): static {
        $this->values[$field] = $value;
        return $this;
    }

}
