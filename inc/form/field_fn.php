<?php

namespace form;

use classes\get;

class field_fn extends field {

    public function mysql_value($value): string {
        return get::fn($value);
    }
}
