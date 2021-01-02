<?php

namespace db\stub;

use JetBrains\PhpStorm\Pure;

class field {

    public string $type = 'text';
    public string $title;
    public bool $length = false;
    public bool $default = false;
    public int $module = 0;
    public int $field = 0;
    public bool $is_default = false;
    public bool $list = true;
    public bool $filter = true;
    public bool $required = true;
    public bool $editable = true;
    public bool $autoincrement = false;
    private string $id;

    #[Pure]
    public static function create($structure): field {
        $field = new self;
        foreach ($structure as $attribute => $value) {
            $field->$attribute = $value;
        }
        return $field;
    }
}