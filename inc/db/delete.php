<?php

namespace db;

use classes\cache;
use classes\db as _db;
use db\query as _query;
use Exception;
use PDOStatement;

class delete extends _query {

    /**
     * @return false|PDOStatement
     * @throws Exception
     */
    public function execute(): bool|PDOStatement {
        $query = 'DELETE FROM ' . $this->table . $this->get_filters();
        cache::break_cache($this->table);
        return _db::query($query, $this->parameters);
    }
}
 