<?php

namespace db;


use JetBrains\PhpStorm\Pure;

abstract class query {

    protected array $parameters = [];
    protected array $fields = [];
    protected $table;
    protected array $joins = [];
    protected array $filters = [];
    protected array $order = [];
    protected array $groupings = [];
    protected string $limit = '';

    public function __construct($table) {
        $this->table = $table;
    }

    public function retrieve($fields): static {
        if ($fields === (array)$fields) {
            foreach ($fields as $field) {
                $this->add_field_to_retrieve($field);
            }
        } else {
            $this->add_field_to_retrieve($fields);
        }
        return $this;
    }

    public function add_field_to_retrieve($field): static {
        $this->fields[] = $field;
        return $this;
    }

    public function set_base_table($table): static {
        $this->table = $table;
        return $this;
    }

    public function add_join($table, $where, $parameters = [], $type = 'LEFT'): static {
        if (!isset($this->joins[$table]['where'])) {
            $this->joins[$table]['where'] = [];
        }
        if (is_array($where)) {
            foreach ($where as $where_clause) {
                $this->joins[$table]['where'][] = $where_clause;
            }
        } else {
            $this->joins[$table]['where'][] = $where;
        }
        $this->joins[$table]['type'] = $type;
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    public function filter_field($field, $value, $operator = '='): static {
        $field_name = 'filter_' . (count($this->parameters) + 1);
        if (strstr($field, '.') === false && strstr($field, ' AS ') === false) {
            $field = '`' . $field . '`';
        }
        $this->filter($field . $operator . ':' . $field_name);
        $this->parameters[$field_name] = $value;
        return $this;
    }

    public function filter($clauses, $parameters = []): static {
        if ($clauses === (array)$clauses) {
            foreach ($clauses as $clause) {
                $this->add_filter($clause);
            }
        } else {
            $this->add_filter($clauses);
        }
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    protected function add_filter($clause, $parameters = []): static {
        $this->filters[] = $clause;
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    public function add_order($term): static {
        $this->order[] = $term;
        return $this;
    }

    public function add_grouping($field): static {
        $this->groupings[] = $field;
        return $this;
    }

    abstract public function execute();

    protected function get_joins(): string {
        $sql = '';
        foreach ($this->joins as $table => $join) {
            $sql .= ' ' . $join['type'] . ' JOIN ' . $table . ' ON ' . implode(' AND ', $join['where']);
        }
        return $sql;
    }

    #[Pure]
    protected function get_filters(): string {
        if ($this->filters) {
            return ' WHERE ' . implode(' AND ', $this->filters);
        }
        return '';
    }

    #[Pure]
    protected function get_groupings(): string {
        if ($this->groupings) {
            return ' GROUP BY ' . implode(',', $this->groupings);
        }
        return '';
    }

    protected function get_limit(): string {
        if ($this->limit) {
            return ' LIMIT ' . $this->limit;
        }
        return '';
    }

    public function set_limit($limit): static {
        $this->limit = $limit;
        return $this;
    }

    #[Pure]
    protected function get_order(): string {
        if ($this->order) {
            return ' ORDER BY ' . implode(',', $this->order);
        }
        return '';
    }

    public function set_order($term): static {
        $this->order = [$term];
        return $this;
    }

}
