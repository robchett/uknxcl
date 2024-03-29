<?php

namespace db;

use classes\cache;
use classes\db as _db;
use db\query as _query;
use PDOStatement;

class update extends _query {

    /** @var array<string, scalar> */
    protected array $values = [];

    public function execute(): bool|PDOStatement {
        $query = 'UPDATE ' . $this->table . ' SET ' . $this->get_values() . $this->get_filters();
        cache::break_cache($this->table);
        return _db::query($query, $this->parameters);
    }

    protected function get_values(): string {
        $sql = [];
        foreach ($this->values as $field => $value) {
            $sql[] = '`' . $field . '`=:' . $field;
            $this->parameters[$field] = $value;
        }
        return implode(', ', $sql);
    }

    /**
     * @param scalar $value
     */
    public function add_value(string $field, mixed $value): static {
        $this->values[$field] = $value;
        return $this;
    }
}