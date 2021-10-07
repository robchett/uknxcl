<?php

namespace form;

use classes\attribute_list;

/**
 * @extends field<string>
 */
class field_password extends field {

    /**
     * @param string[] $class
     * @param string[] $wrapper_class
     * @param string $default
     */
    public function __construct(
        string $field_name,
        int $fid = 0,
        bool $filter = false,
        string $label = '',
        bool $list = true,
        bool $live = true,
        string $pre_text = '',
        string $post_text = '',
        bool $raw = false,
        bool $required = true,
        ?attribute_list $attributes = null,
        bool $hidden =  false,
        bool $disabled = false,
        array $class = ['form-control'],
        array $wrapper_class = [],
        mixed $default = ''
    ) {
        $attributes ??= new attribute_list();
        $attributes->type = 'password';
        parent::__construct(
            $field_name,
            $fid,
            $filter,
            $label,
            $list,
            $live,
            $pre_text,
            $post_text,
            $raw,
            $required,
            $attributes,
            $hidden,
            $disabled,
            $class,
            $wrapper_class,
            $default
        );
    }

    public function get_save_sql(mixed $val): string {
        return md5($val);
    }
}
