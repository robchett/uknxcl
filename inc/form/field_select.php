<?php

namespace form;

use classes\attribute_list;
use classes\table;

/**
 * @extends field<string>
 */
class field_select extends field {
    
    /**
     * @param string[] $options
     * @param string[] $class
     * @param string[] $wrapper_class
     * @param string $default
     */
    public function __construct(
        string $field_name,
        public array $options = [],
        public string $defaultText = 'Please Choose',
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
        $class[] = 'picker';
        $default = array_key_first($options);
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
        $attributes = $this->set_standard_attributes($this->attributes);
        $html = '<select ' . $attributes . '>';
        if (!empty($this->defaultText) && !$this->required) {
            $html .= '<option value="default">' . $this->defaultText . '</option>';
        }
        foreach ($this->options as $k => $v) {
            $html .= '<option value="' . $k . '" ' . ($this->get_value($form) == $k ? 'selected="selected"' : '') . '>' . $v . '</option>';

        }
        $html .= '</select>';
        return $html;
    }

    public function do_validate(form $form): array {
        /*if ($this->required && (empty($this->get_value($form))))
            $error_array[$this->field_name] = $this->field_name . ' is required field';*/
        if ($this->get_value($form) == 'default' && $this->required) {
            return [false, $this->field_name . ' please choose an option'];
        }
        return [true, ''];
    }
}
