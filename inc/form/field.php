<?php

namespace form;

use classes\attribute_callable;
use classes\attribute_list;
use classes\db;
use classes\get;
use classes\icon;
use classes\interfaces\model_interface;
use classes\table;
use core;
use html\node;
use module\cms\controller;

/**
 * @template T
 */
class field extends node {

    public attribute_list $attributes;
    /** @param T? $value */
    public null|int|string|array $value = null;

    /**
     * @param T $default
     * @param string[] $class
     * @param string[] $wrapper_class
     */
    public function __construct(
        public string $field_name,
        public int $fid = 0,
        public bool $filter = false,
        public string $label = '',
        public bool $list = true,
        public bool $live = true,
        public string $pre_text = '',
        public string $post_text = '',
        public bool $raw = false,
        public bool $required = true,
        ?attribute_list $attributes = null,
        public bool $hidden =  false,
        public bool $disabled = false,
        public array $class = ['form-control'],
        public array $wrapper_class = [],
        public mixed $default = ''
    ) {
        $this->attributes = $attributes ?? new attribute_list();
        $this->attributes->type ??= 'text';
        if (!$this->label) {
            $this->label = ucwords(str_replace('', ' ', $this->field_name));
        }
    }

    public static function sanitise_from_db(string $value): mixed {
        return $value;
    }

    public function get_html_wrapper(form $form): string {
        $html = $this->pre_text;

        if (!$this->hidden && $this->label) {
            $html .= node::create('label.control-label.col-md-' . $form->bootstrap[0], ['for' => $this->field_name, 'id' => $this->field_name . '_wrapper'], $this->label);
        }
        $html .= node::create('div.control.col-md-' . $form->bootstrap[1], [], $this->get_html($form));
        $html .= $this->post_text;
        return $html;
    }

    public function get_html(form $form): string {
        $attributes = $this->set_standard_attributes($this->attributes);
        return '<input ' . $attributes . ' value="' . htmlentities((string) $this->get_value($form)) . '"/>';
    }

    public function set_standard_attributes(attribute_list $attributes): attribute_list {
        if ($this->hidden) {
            $attributes->type = 'hidden';
        }
        $attributes->name ??= $this->field_name;
        $attributes->id ??= $this->field_name;
        if ($this->disabled) {
            $attributes->disabled = 'disabled';
        }
        if ($this->required) {
            $this->class[] = 'required';
        }
        $attributes->class = $this->class;
        return $attributes;
    }

    /**
     * @return T
     * @psalm-suppress MixedInferredReturnType
     */
    public function get_value(form $form): mixed {
        /** @psalm-suppress MixedReturnStatement  */
        return $form->{$this->field_name};
    }

    /** @return array{bool, string} */
    public function do_validate(form $form): array {
        if ($this->required && empty($this->get_value($form))) {
            return [false, $this->field_name . ' is required field'];
        }
        return [true, ''];
    }

    public function get_class(): bool|string {
        if (!empty($this->class)) {
            return 'class="' . implode(' ', $this->class) . '"';
        }
        return false;
    }

    public function get_wrapper_class(form $form): string {
        $classes = array_merge($this->wrapper_class, $form->field_wrapper_class);
        $classes[] = get::__basename($this) . '_wrapper'; 
        return implode(' ', $classes);
    }

    public function set_from_request(form $form): void {
        $form->{$this->field_name} = (isset($_REQUEST[$this->field_name]) ? ($this->raw ? $_REQUEST[$this->field_name] : strip_tags((string) $_REQUEST[$this->field_name])) : '');
    }

    /**
     * @param T $value
     * @param class-string $object_class
     */
    public function get_cms_list_wrapper(model_interface $form, mixed $value, string $object_class, int $id): string {
        return (string) $value;
    }

    /**
     * @param T $val
     */
    public function get_save_sql(mixed $val): string {
        return $this->mysql_value($val);
    }

    /**
     * @param T $value
     */
    public function mysql_value(mixed $value): string {
        return (string) $value;
    }
}
