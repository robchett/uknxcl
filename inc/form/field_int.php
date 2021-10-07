<?php

namespace form;

use classes\attribute_list;
use classes\table;

/**
 * @extends field<int>
 */
class field_int extends field {
    
    /**
     * @param string[] $class
     * @param string[] $wrapper_class
     * @param int $default
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
        $attributes->type = 'number';
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

    public function set_from_request(form $form): void {
        $form->{$this->field_name} = (isset($_REQUEST[$this->field_name]) && !empty($_REQUEST[$this->field_name]) ? $_REQUEST[$this->field_name] : null);
    }

    public function do_validate(form $form): array {
        if ($this->required && !$this->get_value($form)) {
            return [false, $this->field_name . ' is required field'];
        }
        return [true, ''];
    }
}
