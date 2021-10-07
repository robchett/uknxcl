<?php

namespace form;

use classes\attribute_list;
use classes\table;

/**
 * @extends field<float>
 */
class field_float extends field {
    
    /**
     * @param string[] $class
     * @param string[] $wrapper_class
     * @param float $default
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
        mixed $default = 0
    ) {
        $attributes ??= new attribute_list();
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

    public function do_validate(form $form): array {
        $val = $this->get_value($form);
        if ($this->required && empty($val)) {
            return [false, $this->field_name . ' is required field'];
        } else if (!empty($val) && !is_numeric($val)) {
            return [false, $this->field_name . ' is not_numeric'];
        }
        return [true, ''];
    }
}