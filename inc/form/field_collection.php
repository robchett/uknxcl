<?php

namespace form;

use classes\table_array;
use Exception;
use JetBrains\PhpStorm\Pure;

class field_collection extends table_array {

    /**
     * @param $field_name
     *
     * @return mixed
     * @throws Exception
     */
    public function get_field($field_name): mixed {
        if ($this->has_field($field_name)) {
            return $this[$field_name];
        } else {
            throw new Exception('field:' . $field_name . ' not found');
        }
    }

    /**
     * @param $name
     * @return bool
     */
    #[Pure]
    public function has_field($name): bool {
        return isset($this[$name]);
    }

}
 