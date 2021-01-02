<?php

namespace db;

use classes\db as _db;
use db\query as _query;
use PDOStatement;

class select extends _query {

    /**
     * @return false|PDOStatement
     */
    public function execute(): bool|PDOStatement {
        $query = 'SELECT ' . $this->get_fields() . ' FROM ' . $this->table . $this->get_joins() . $this->get_filters() . $this->get_groupings() . $this->get_order() . ' ' . $this->get_limit();
        return _db::query($query, $this->parameters);
    }

    protected function get_fields(): string {
        $fields = [];
        foreach ($this->fields as $field) {
            if (strstr($field, ' ') === false && strstr($field, '.') === false && strstr($field, '(') === false) {
                $fields[] = '`' . $field . '`';
            } else {
                $fields[] = $field;
            }
        }
        return implode(',', $fields);
    }

}
 