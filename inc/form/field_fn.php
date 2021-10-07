<?php

namespace form;

use classes\get;

/**
 * @extends field<string>
 */
class field_fn extends field {

    public function mysql_value(mixed $value): string {
        return get::fn($value);
    }
}
