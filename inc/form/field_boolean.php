<?php

namespace form;

use classes\attribute_callable;
use classes\attribute_list;
use classes\interfaces\model_interface;
use classes\table;
use html\node;

/**
 * @extends field<bool>
 */
class field_boolean extends field {

    /**
     * @param string[] $class
     * @param string[] $wrapper_class
     * @param bool $default
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
        mixed $default = false
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

    public function do_validate(form $form): array {
        return [true, ''];
    }

    public function set_from_request(form $form): void {
        $form->{$this->field_name} = isset($_REQUEST[$this->field_name]);
    }

    public function get_html_wrapper(form $form): string {
        $html = $this->pre_text;
        $html .= node::create('div.col-md-offset-' . $form->bootstrap[0] . '.col-md-' . $form->bootstrap[1] . ' div.checkbox label', [], $this->get_html($form) . $this->label);
        $html .= $this->post_text;
        return $html;
    }

    public function get_html(form $form): string {
        $attributes = $this->set_standard_attributes($this->attributes);
        if ($this->required) {
            $this->class[] = 'required';
            $this->required = false;
        }
        if ($this->get_value($form)) {
            $attributes->checked = 'checked';
        }
        return '<input ' . $attributes . '/>';
    }

    public function get_cms_list_wrapper(model_interface $form, mixed $value, string $object_class, int $id): string {
        $this->attributes->dataAjaxClick = attribute_callable::create([$form::class, 'do_cms_update']);
        $this->attributes->dataAjaxPost = '{"field":"' . $this->field_name . '", "value":' . ((int)!$value) . ',"id":' . $id . '}';
        $this->attributes->id = ($this->attributes->id ?? $this->field_name) . '_' . $id;
        $this->attributes->dataAjaxShroud = '#' . $this->field_name . '_' . $form->get_primary_key();
        $realForm = $form->get_form();
        $realForm->{$this->field_name} = $value;
        return $this->get_html($realForm);
    }

    public function mysql_value(mixed $value): string {
        return $value ? '1' : '0';
    }
}
