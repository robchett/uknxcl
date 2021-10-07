<?php

namespace db;

abstract class query {

    /** @var array<string, scalar> */
    protected array $parameters = [];
    /** @var string[] */
    protected array $fields = [];
    protected string $table;
    /** @var array<string, array{type: string, where: string[]}> */
    protected array $joins = [];
    /** @var string[] */
    protected array $filters = [];
    /** @var string[] */
    protected array $order = [];
    /** @var string[] */
    protected array $groupings = [];
    protected string $limit = '';

    public function __construct(string $table) {
        $this->table = $table;
    }

    /** @param string[]|string $fields */
    public function retrieve(array|string $fields): static {
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $this->add_field_to_retrieve($field);
            }
        } else {
            $this->add_field_to_retrieve($fields);
        }
        return $this;
    }

    public function add_field_to_retrieve(string $field): static {
        $this->fields[] = $field;
        return $this;
    }

    public function set_base_table(string $table): static {
        $this->table = $table;
        return $this;
    }

    /**
     * @param string[]|string $where
     * @param array<string, scalar> $parameters
     */
    public function add_join(string $table, array|string $where, array $parameters = [], string $type = 'LEFT'): static {
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

    public function filter_field(string $field, string|int|float|bool $value, string $operator = '='): static {
        $field_name = 'filter_' . (count($this->parameters) + 1);
        if (strstr($field, '.') === false && strstr($field, ' AS ') === false) {
            $field = '`' . $field . '`';
        }
        $this->filter($field . $operator . ':' . $field_name);
        $this->parameters[$field_name] = $value;
        return $this;
    }

    /**
     * @param string[]|string $clauses
     * @param array<string, scalar> $parameters
     */
    public function filter(array|string $clauses, array $parameters = []): static {
        if (is_array($clauses)) {
            foreach ($clauses as $clause) {
                $this->add_filter($clause);
            }
        } else {
            $this->add_filter($clauses);
        }
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    /** @param array<string, scalar> $parameters */
    protected function add_filter(string $clause, array $parameters = []): static {
        $this->filters[] = $clause;
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    public function add_order(string $term): static {
        $this->order[] = $term;
        return $this;
    }

    public function add_grouping(string $field): static {
        $this->groupings[] = $field;
        return $this;
    }

    abstract public function execute(): mixed;

    protected function get_joins(): string {
        $sql = '';
        foreach ($this->joins as $table => $join) {
            $sql .= ' ' . $join['type'] . ' JOIN ' . $table . ' ON ' . implode(' AND ', $join['where']);
        }
        return $sql;
    }

    protected function get_filters(): string {
        if ($this->filters) {
            return ' WHERE ' . implode(' AND ', $this->filters);
        }
        return '';
    }

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

    public function set_limit(string $limit): static {
        $this->limit = $limit;
        return $this;
    }

    protected function get_order(): string {
        if ($this->order) {
            return ' ORDER BY ' . implode(',', $this->order);
        }
        return '';
    }

    public function set_order(string $term): static {
        $this->order = [$term];
        return $this;
    }

}
