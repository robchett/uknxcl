<?php

namespace classes;

class tableOptions {

    /**
     * @param array<string, string> $join
     * @param array<string, scalar> $where_equals
     * @param array<string, scalar> $parameters
     */
    public function __construct(
        public string $limit = '',
        public array $join = [], 
        public array $where_equals = [],
        public array $parameters = [],
        public string $where = '',
        public string $order = '',
        public string $group = '',
        public bool $retrieve_unlive = false,
        public bool $retrieve_deleted = true
    ) {
        
        
    }

}