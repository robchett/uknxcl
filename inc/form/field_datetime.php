<?php

namespace form;

use classes\attribute_list;
use classes\interfaces\model_interface;
use classes\table;

/**
 * @extends field<int>
 */
class field_datetime extends field
{

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
        $attributes->pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}';
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

    public static function sanitise_from_db(string $value): bool|int
    {
        return strtotime($value);
    }

    public function get_html(form $form): string
    {
        $attributes = $this->set_standard_attributes($this->attributes);
        return '<input ' . $attributes . ' value="' . date('d/m/Y h:i:s', $this->get_value($form)) . '"/>';
    }

    public function mysql_value(mixed $value): string
    {
        return date('Y-m-d h:i:s', $value);
    }

    public function get_cms_list_wrapper(model_interface $form, mixed $value, string $object_class, int $id): string
    {
        return date('d/m/Y @h:i:s', $value) ?: '';
    }
}
