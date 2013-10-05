<?php
namespace core\db;

use core\classes\db;
use db\query;

abstract class select extends query {

    /**
     * @return \PDOStatement
     */
    public function execute() {
        $query = 'SELECT ' . implode(', ', $this->fields) . ' FROM ' . $this->table . $this->get_joins() . $this->get_filters() . $this->get_groupings() . ' ' . $this->get_limit();
        return db::query($query, $this->parameters);
    }

}
 