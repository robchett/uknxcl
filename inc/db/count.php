<?php

namespace db;

use classes\db;
use db\query as _query;


class count extends _query {

    public function execute(): int {
        $query = 'SELECT COUNT(' . $this->fields[0] . ') AS count FROM ' . $this->table . $this->get_joins() . $this->get_filters() . $this->get_groupings() . ' ' . $this->get_limit();
        $res = db::query($query, $this->parameters);
        return (int) $res->fetchObject()->count;
    }
}
 