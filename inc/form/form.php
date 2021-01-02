<?php

namespace form;

use classes\ajax;
use classes\get;
use Exception;
use form\field as _field;
use form\field_file as _field_file;
use html\node;
use ReflectionClass;
use ReflectionException;

/**
 * Class form
 * @package form
 */
abstract class form {

    public array $bootstrap = [2, 10, 'form-horizontal'];

    /**
     * @var string
     */
    public string $action = '';
    /**
     * @var string
     */
    public string $method = 'post';
    /**
     * @var string
     */
    public string $content = '';
    /** @var field[] $fields */
    public array $fields = [];
    /**
     * @var string
     */
    public string $h2 = '';
    /**
     * @var string
     */
    public string $description = '';
    /**
     * @var
     */
    public $target;
    /**
     * @var bool
     */
    public bool $use_ajax = true;
    /**
     * @var string
     */
    public string $pre_text = '';
    /**
     * @var string
     */
    public string $post_fields_text = '';
    /**
     * @var string
     */
    public string $post_text = '';
    /**
     * @var string
     */
    public string $submit = 'Submit';
    /**
     * @var string
     */
    public string $id = 'form';
    /**
     * @var bool
     */
    public bool $submittable = true;
    /**
     * @var bool
     */
    public bool $has_submit = true;
    /**
     * @var array
     */
    public array $attributes = [];
    /**
     * @var array
     */
    public array $validation_errors = [];
    public array $wrapper_class = ['form_wrapper'];
    public array $field_wrapper_class = ['form-group'];
    public array $submit_attributes = ['type' => 'submit'];
    protected $table_object;

    /**
     * @param $fields
     */
    public function __construct($fields) {
        $this->fields = [];
        foreach ($fields as $field) {
            $this->add_field($field);
        }
    }

    public function add_field(_field $field) {
        $field->parent_form = $this;
        $this->{$field->field_name} = $field->value;
        if ($field instanceof _field_file) {
            $this->use_ajax = false;
        }
        $this->fields[$field->field_name] = $field;
    }

    /**
     * @return field
     * @throws ReflectionException
     */
    public static function create(): object {
        $args = func_get_args();
        $class_name = 'form\\' . $args[0];
        unset($args[0]);
        $class = new ReflectionClass($class_name);
        return $class->newInstanceArgs($args);
    }

    /**
     * @param      $object
     * @param bool $change_target
     */
    public function set_from_object($object, $change_target = true) {
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

    public function get_table_class(): bool|string {
        return get::__class_name($this->get_table_object());
    }

    public function get_table_object() {
        if (!$this->table_object) {
            trigger_error('Trying to access a forms table object before set_from_object has been called.');
        }
        return $this->table_object;
    }

    /**
     * @param $field_name
     * @return bool
     */
    public function remove_field($field_name): bool {
        if (!cms) {
            unset($this->fields[$field_name]);
            return true;
        }
        return false;
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

    /**
     *
     */
    public function set_from_request() {
        foreach ($this->fields as $field) {
            if (isset($_REQUEST[$field->field_name])) {
                $field->set_from_request();
            }
        }
    }

    /**
     * @return bool
     */
    public function do_validate(): bool {
        foreach ($this->fields as $field) {
            $field->do_validate($this->validation_errors);
        }
        return count($this->validation_errors) ? false : true;
    }

    /**
     * @return bool
     */
    abstract public function do_submit(): bool;

    /**
     *
     */
    public function do_invalidate_form() {
        foreach ($this->validation_errors as $key => $val) {
            $field = $this->get_field_from_name($key);
            $field->add_class('has-error');
            $field->add_wrapper_class('has-error');
        }
        ajax::update((string)$this->get_html());
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function get_field_from_name($name): mixed {
        if ($this->has_field($name)) {
            return $this->fields[$name];
        }
        throw new Exception('Field ' . $name . ' not found in ' . get_called_class());
    }

    public function has_field($name): bool {
        return isset($this->fields[$name]);
    }

    /**
     * @return string
     */
    public function get_html(): string {
        if (!$this->use_ajax) {
            if (!$this->action) {
                $this->action = '/index.php?module=' . get_class($this) . '&act=do_form_submit&no_ajax=on&ajax_origin=' . $this->id;
            }
            $this->attributes['target'] = 'form_target_' . $this->id;
            $this->attributes['enctype'] = 'multipart/form-data';
        }
        $html = node::create('div#' . $this->id . '_wrapper.' . implode('.', $this->wrapper_class));
        $this->attributes = array_merge(['name' => $this->id, 'method' => $this->method, 'action' => !empty($this->action) ? $this->action : get_class($this) . ':do_form_submit', 'data-ajax-shroud' => '#' . $this->id,], $this->attributes);
        $this->attributes['class'][] = $this->bootstrap[2];
        if ($this->h2) {
            $html->nest(node::create('h2.form_title', [], $this->h2));
        }
        $html->nest($this->get_html_body());
        if (!$this->use_ajax) {
            $html->add_child(node::create('iframe#form_target_' . $this->id . '.form_frame', ['style' => 'display:none', 'src' => '/blank.html', 'name' => 'form_target_' . $this->id]));
        }
        return $html;
    }

    /**
     * @return node
     */
    public function get_html_body(): node {
        $form = node::create('form#' . $this->id . '.' . ($this->use_ajax ? 'ajax' : 'noajax'), $this->attributes);
        if (!empty($this->pre_fields_text)) {
            $form->nest(node::create('div.pre_fields_text', [], $this->pre_fields_text));
        }
        $form->nest(...$this->get_fields_html());
        if (!empty($this->post_fields_text)) {
            $form->nest(node::create('div.post_fields_text', [], $this->post_fields_text));
        }
        $form->nest($this->get_hidden_fields());
        if (!empty($this->post_text)) {
            $form->nest(node::create('div.post_text', [], $this->post_text));
        }
        return $form;
    }

    /**
     * @return array
     */
    public function get_fields_html(): array {
        $field_sets = [];
        $fields = [];
        $field_set_title = '';
        foreach ($this->fields as $field) {
            if (!$field->hidden) {
                if (isset($field->fieldset) && $field_set_title != $field->fieldset) {
                    $field_set = $this->get_field_set($fields, count($field_sets), $field_set_title);
                    if ($field_set) {
                        $field_sets[] = $field_set;
                        $field_set_title = $field->fieldset;
                        $fields = [];
                    }
                }
                if ($inner = $field->get_html_wrapper()) {
                    $fields[] = node::create('div#' . $this->id . '_field_' . $field->field_name . $field->get_wrapper_class(), ['data-for' => $this->id], $inner);
                }
            }
        }
        if ($this->has_submit) {
            $fields[] = $this->get_submit();
        }
        $field_set = $this->get_field_set($fields, count($field_sets), $field_set_title);
        if ($field_set) {
            $field_sets[] = $field_set;
        }
        return $field_sets;
    }

    /**
     * @param $fields
     * @param int $index
     * @param string $title
     *
     * @return false|node
     */
    protected function get_field_set($fields, $index = 1, $title = ''): bool|node {
        if ($fields) {
            $field_set = node::create('fieldset.fieldset_' . $index, []);
            if ($title) {
                $field_set->nest("<legend>{$title}<legend>");
            }
            $field_set->nest(...$fields);
            return $field_set;
        }
        return false;
    }

    /**
     * @return node
     */
    public function get_submit(): node {
        if ($this->has_submit) {
            $field = node::create('div.form-group div.col-md-offset-' . $this->bootstrap[0] . '.col-md-' . $this->bootstrap[1], [], node::create('button.btn.btn-default', $this->submit_attributes, $this->submit));
            if (!$this->submittable) {
                $field->add_attribute('disabled', 'disabled');
            }
            return $field;
        }
        return node::create('');
    }

    /**
     * @return node
     */
    public function get_hidden_fields(): node {
        $hidden = [];
        foreach ($this->fields as $field) {
            if ($field->hidden) {
                $hidden[] = $field->get_html_wrapper();
            }

        }
        return $hidden ? node::create('div.hidden', [], $hidden) : node::create('-');
    }
}