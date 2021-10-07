<?php

namespace form;

use classes\attribute_list;
use classes\table;

/**
 * @extends field<int[]>
 */
class field_checkboxes extends field {

    /**
     * @param array<int, string> $options
     * @param string[] $class
     * @param string[] $wrapper_class
     * @param int[] $default
     */
    public function __construct(
        string $field_name,
        int $fid = 0,
        public array $options = [],
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
        mixed $default = []
    ) {
        $attributes ??= new attribute_list();
        $attributes->type = 'checkbox';
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

    public function get_html(form $form): string {
        $html = '';
        foreach ($this->options as $key => $value) {
            $html .= $this->get_inner_html($form, $key, $value);
        }
        return $html;
    }

    protected function get_inner_html(form $form, int $key, string $value): string {
        return '
        <label class="checkbox">
            <input type="checkbox" name="' . $this->field_name . '[]" value="' . $key . '" ' . (in_array($key, $this->get_value($form)) ? 'checked="checked"' : '') . '>' . $value . '
        </label>';
    }

    public function set_from_request(form $form): void {
        $form->{$this->field_name} = ($_REQUEST[$this->field_name] ?? []);
    }
}
