<?php
class form extends html_element {
    public $action = '';
    public $method = 'post';
    public $content = '';
    public $fields = array();
    public $h2 = '';
    public $description = '';
    public $target;
    public $use_ajax = true;
    public $pre_text = '';
    public $post_submit_text = '';
    public $post_fields_text = '';
    public $post_text = '';
    public $submit = 'Submit';
    public $id = 'form';
    public $submittable = true;
    public $has_submit = true;
    public $attributes = array();
    public $validation_errors = array();

    public function  __construct(array $fields) {
        $this->fields = $fields;
        foreach ($this->fields as $field) {
            $field->parent_form = $this;
            $this->{$field->field_name} = $field->value;
        }
        foreach ($this->fields as $field) {
            if (get_class($field) == 'field_file') {
                $this->use_ajax = false;
                $this->action = '/index.php?module=' . get_class($this) . '&act=do_submit&no_ajax=on&ajax_origin=' . $this->id;
                $this->attributes['target'] = 'form_target_' .  get_class($this);
                $this->attributes['enctype'] = 'multipart/form-data';
                break;
            }
        }
    }

    /**@return field */
    public static function create() {
        $args = func_get_args();
        $obj = $args[0];
        unset($args[0]);
        $class = new ReflectionClass($obj);
        return $class->newInstanceArgs($args);
    }

    public function set_from_object($object) {
        foreach ($this->fields as $field) {
            if (isset($object->{$field->field_name})) {
                $this->{$field->field_name} = $object->{$field->field_name};
            }
        }
    }

    public function remove_field($field_name) {
        if (!cms) {
            foreach ($this->fields as $key => $field) {
                if ($field->field_name == $field_name) {
                    unset($this->fields[$key]);
                    return true;
                }
            }
        }
        return false;
    }

    public function do_submit() {
        $this->set_from_request();
        $this->do_validate();
        if (!empty($this->validation_errors)) {
            $this->do_invalidate_form();
            return false;
        }
        return true;
    }

    public function set_from_request() {
        foreach ($_REQUEST as $key => $val) {
            if ($field = $this->get_field_from_name($key)) {
                $field->set_from_request();
            }
        }
    }

    /** @return field */
    public function get_field_from_name($name) {
        foreach ($this->fields as $field) {
            if ($field->field_name == $name) {
                return $field;
            }
        }
        return false;
    }

    public function get_html() {
        $this->attributes = array_merge(array(
                'name' => $this->id,
                'method' => $this->method,
                'action' => !empty($this->action) ? $this->action : get_class($this) . ':' . 'do_submit',
                'data-ajax-shroud' => '#' . $this->id,
            ), $this->attributes
        );
        $html = html_node::create('div#' . $this->id . '_wrapper')->nest([
                html_node::create('h2.form_title', $this->h2),
                $this->get_html_body()
            ]
        );
        if (!$this->use_ajax) {
            $html->add_child(html_node::create('iframe#form_target_' . $this->id . '.form_frame', '', array('width' => 1, 'height' => 1, 'frame-border' => 0, 'border' => 0, 'src' => '/inc/module/blank.html', 'name' => 'form_target_' . $this->id)));
        }
        return $html;
    }

    public function get_html_body(){
        $form = html_node::create('form#' . $this->id . '.' . ($this->use_ajax ? 'ajax' : 'noajax'), '', $this->attributes);
        if (!empty($this->pre_fields_text)) {
            $form->nest(html_node::create('div.pre_fields_text', $this->pre_fields_text));
        }
        $form->nest($this->get_fields_html());
        if (!empty($this->post_fields_text)) {
            $form->nest(html_node::create('div.post_fields_text', $this->post_fields_text));
        }
        $form->nest($this->get_hidden_fields());
        if ($this->has_submit) {
            $form->nest($this->get_submit());
        }
        if (!empty($this->post_submit_text)) {
            $form->nest(html_node::create('div.post_submit_text', $this->post_submit_text));
        }
        if (!empty($this->post_text)) {
            $form->nest(html_node::create('div.post_text', $this->post_text));
        }
        return $form;
    }

        public
        function get_fields_html() {
            $fieldsets = array();
            $fields = array();
            foreach ($this->fields as $field) {
                if (!$field->hidden) {
                    if (isset($field->fieldset)) {
                        $fieldsets[] = html_node::create('fieldset.fieldset_' . count($fieldsets) . ' ul')->nest($fields);
                        $fields = array();
                    }
                    if($inner = $field->get_html_wrapper()) {
                    $fields[] = html_node::create('li', $inner, array('data-for' => $this->id, 'class' => $field->get_wrapper_class()));
                    }
                }
            }
            $fieldsets[] = html_node::create('fieldset.fieldset_' . count($fieldsets) . ' ul')->nest($fields);
            return $fieldsets;
        }

        public
        function get_hidden_fields() {
            $html = html_node::create('ul.hidden');
            foreach ($this->fields as $field) {
                if ($field->hidden)
                    $html->add_child(html_node::create('li', $field->get_html_wrapper(), array('data-for' => $this->id, 'class' => $field->get_wrapper_class())));
            }
            return $html;
        }

        public
        function get_submit() {
            if ($this->has_submit) {
                $field = html_node::create('input.submit', '', array('type' => 'submit', 'data-for' => $this->id, 'name' => $this->submit));
                if (!$this->submittable) {
                    $field->add_attribute('disabled', 'disabled');
                }
                return $field;
            }
            return html_node::create('');
        }

        public
        function do_validate() {
            foreach ($this->fields as $field) {
                $field->do_validate($this->validation_errors);
            }
        }

        public
        function do_invalidate_form() {
            $html = '<ul class="error_list">';
            foreach ($this->validation_errors as $key => $val) {
                $field = $this->get_field_from_name($key);
                $field->add_class('err');
                $html .= '<li>' . $key . ' ' . $val . '</li>';
            }

            $html .= '</ul>';
            ajax::update($this->get_html()->get());
            //  ajax::inject('#' . $this->id, 'prepend', $html, '#' . $this->id . '_error');
        }
    }
