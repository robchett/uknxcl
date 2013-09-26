<?php
namespace db;
class update extends query {

    protected $values = [];

    public function execute() {
        $query = 'UPDATE ' . $this->table . ' SET ' . $this->get_values() . $this->get_filters();
        return \db::query($query, $this->parameters);
    }

    protected function get_values() {
        $sql = [];
        foreach ($this->values as $field => $value) {
            $sql[] = '`' . $field . '`=:' . $field;
            $this->parameters[$field] = $value;
        }
        return implode(', ', $sql);
    }

    public function add_value($field, $value) {
        $this->values[$field] = $value;
        return $this;
    }
}
 