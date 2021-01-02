<?php

namespace form;

class field_string extends field {

    public function get_database_create_query(): string {
        return 'varchar(255)';
    }
}
