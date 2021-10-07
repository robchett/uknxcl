<?php

namespace form;

use classes\ajax;
use classes\attribute_list;
use classes\get;
use classes\interfaces\model_interface;
use classes\table;
use Exception;
use form\field_file;
use html\node;
use ReflectionClass;

abstract class form {

    /**
     * @var array{int, int, string}
     */
    public array $bootstrap = [2, 10, 'form-horizontal'];
    public string $action = '';
    public string $method = 'post';
    public string $content = '';
    /** @var field[] $fields */
    public array $fields = [];
    public string $h2 = '';
    public string $description = '';
    public bool $use_ajax = true;
    public string $pre_text = '';
    public string $post_fields_text = '';
    public string $pre_fields_text = '';
    public string $post_text = '';
    public string $submit = 'Submit';
    public string $id = 'form';
    public bool $submittable = true;
    public bool $has_submit = true;
    public attribute_list $attributes;
    /** @var array<string, string> */
    public array $validation_errors = [];
    /** @var string[] */
    public array $wrapper_class = ['form_wrapper'];
    /** @var string[] */
    public array $field_wrapper_class = ['form-group'];
    public attribute_list $submit_attributes;
    protected model_interface|form $table_object;

    /**
     * @param field[] $fields
     */
    public function __construct(array $fields) {
        $this->attributes = new attribute_list();
        $this->submit_attributes = new attribute_list(type: 'submit', class: ['btn', 'btn-default']);
        $this->fields = [];
        foreach ($fields as $field) {
            $this->add_field($field);
        }
    }

    public function add_field(field $field): void {
        $this->{$field->field_name} = $field->value ?? $field->default;
        if ($field instanceof field_file) {
            $this->use_ajax = false;
        }
        $this->fields[$field->field_name] = $field;
    }

    public function set_from_object(model_interface|form $object, bool $change_target = true): void {
        $this->table_object = $object;
        foreach ($this->fields as $field) {
            if (isset($object->{$field->field_name})) {
                $this->{$field->field_name} = $object->{$field->field_name};
            }
        }
        if ($change_target) {
            $this->action = get_class($object) . ':do_form_submit';
        }
    }

    public function do_form_submit(): bool {
        $this->set_from_request();
        $ok = $this->do_validate();
        if ($ok) {
            $this->do_submit();
            return true;
        } else {
            $this->do_invalidate_form();
            return false;
        }
    }

    public function set_from_request(): void {
        foreach ($this->fields as $field) {
            if (isset($_REQUEST[$field->field_name])) {
                $field->set_from_request($this);
            }
        }
    }

    public function do_validate(): bool {
        foreach ($this->fields as $field) {
            [$ok, $error] = $field->do_validate($this); 
            if (!$ok) {
                $this->validation_errors[$field->field_name] = $error;
            }
        }
        return !count($this->validation_errors);
    }

    abstract public function do_submit(): bool;

    public function do_invalidate_form(): void {
        foreach ($this->validation_errors as $key => $_) {
            $field = $this->get_field_from_name($key);
            $field->class[] ='has-error';
            $field->wrapper_class[] = 'has-error';
        }
        ajax::update($this->get_html());
    }

    /**
     * @throws Exception
     */
    public function get_field_from_name(string $name): field {
        if ($this->has_field($name)) {
            return $this->fields[$name];
        }
        throw new Exception('Field ' . $name . ' not found in ' . get_called_class());
    }

    public function has_field(string $name): bool {
        return isset($this->fields[$name]);
    }

    public function get_html(): string {
        if (!$this->use_ajax) {
            if (!$this->action) {
                $this->action = '/index.php?module=' . get_class($this) . '&act=do_form_submit&no_ajax=on&ajax_origin=' . $this->id;
            }
            $this->attributes->target = 'form_target_' . $this->id;
            $this->attributes->enctype = 'multipart/form-data';
        }
        $this->attributes->name ??= $this->id;
        $this->attributes->method ??= $this->method;
        $this->attributes->action ??= !empty($this->action) ? $this->action : get_class($this) . ':do_form_submit';
        $this->attributes->dataAjaxShroud ??= '#' . $this->id;
        $this->attributes->class[] = $this->bootstrap[2];
        $html = '';
        if ($this->h2) {
            $html .= '<h2 class="form_title">' . $this->h2 . '</h2>';
        }
        $html .= $this->get_html_body();
        if (!$this->use_ajax) {
            $html .= '<iframe id="form_target_' . $this->id . '" class="form_frame" style="display:none" src="/blank.html" name="form_target_' . $this->id . '"></iframe>';
        }
        return '<div id="' . $this->id . '_wrapper" class="' . implode(' ', $this->wrapper_class) . '">' . $html . '</div>';
    }

    public function get_html_body(): string {
        $this->attributes->class[] = ($this->use_ajax ? 'ajax' : 'noajax');
        $this->attributes->id = $this->id;
        $form = '<form' . $this->attributes . '>';
        if (!empty($this->pre_fields_text)) {
            $form .= '<div class="pre_fields_text">' . $this->pre_fields_text . '</div>';
        }
        $form .= $this->get_fields_html();
        if (!empty($this->post_fields_text)) {
            $form .= '<div class="post_fields_text">' . $this->post_fields_text . '</div>';
        }
        $form .= $this->get_hidden_fields();
        if (!empty($this->post_text)) {
            $form .= '<div class="post_text">' . $this->post_text . '</div>';
        }
        $form .= '</form>';
        return $form;
    }

    public function get_fields_html(): string {
        $field_sets = [];
        $fields = [];
        $field_set_title = '';
        foreach ($this->fields as $field) {
            if (!$field->hidden && ($inner = $field->get_html_wrapper($this))) {
                $fields[] = "<div id='{$this->id}_field_{$field->field_name}' class='{$field->get_wrapper_class($this)}' dataFor='$this->id'>$inner</div>";
            }
        }
        if ($this->has_submit) {
            $fields[] = $this->get_submit();
        }
        $field_set = $this->get_field_set($fields, count($field_sets), $field_set_title);
        if ($field_set) {
            $field_sets[] = $field_set;
        }
        return implode('', $field_sets);
    }

    /**
     * @param string[] $fields
     */
    protected function get_field_set(array $fields, int $index = 1, string $title = ''): string {
        if ($fields) {
            $field_set = '<fieldset class="fieldset_' . $index . '">';
            if ($title) {
                $field_set .= "<legend>{$title}</legend>";
            }
            $field_set .= implode("", $fields);
            $field_set .= '</fieldset>';
            return $field_set;
        }
        return '';
    }

    public function get_submit(): string {
        if (!$this->has_submit) {
            return '';
        }

        if (!$this->submittable) {
            $this->submit_attributes->disabled = 'disabled';
        }
        return "<div class='form-group'><div class='col-md-offset-{$this->bootstrap[0]} col-md-{$this->bootstrap[1]}'><button{$this->submit_attributes}>$this->submit</button></div></div>";
    }

    public function get_hidden_fields(): string {
        $hidden = [];
        foreach ($this->fields as $field) {
            if ($field->hidden) {
                $hidden[] = $field->get_html_wrapper($this);
            }

        }
        return $hidden ? '<div class="hidden">' . implode('', $hidden) . '</div>' : '';
    }
}