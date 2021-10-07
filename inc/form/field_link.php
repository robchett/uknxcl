<?php

namespace form;

use classes\attribute_list;
use classes\collection;
use classes\get;
use classes\interfaces\model_interface;
use classes\table;
use classes\table_array;
use classes\tableOptions;

/**
 * @extends field<int>
 */
class field_link extends field {

    public tableOptions $options;
    
    /**
     * @param string[] $class
     * @param string[] $wrapper_class
     * @param class-string<model_interface> $link_module
     * @param string|string[] $link_field
     * @param int $default
     */
    public function __construct(
        string $field_name,
        public string $link_module,
        public array|string $link_field,
        public string $defaultText = 'Please Choose',
        tableOptions $options = null,
        public string $order = '',
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
        $this->options = $options ?? new tableOptions();
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

    public function get_cms_list_wrapper(model_interface $form, mixed $value, string $object_class, int $id): string {
        $class = $this->link_module;
        $field_name = $this->link_field ?: 'title';
        $object = $class::getFromId($value);
        return (string) ($object && $object->get_primary_key() ? $object->$field_name : '-');
    }

    public function get_html(form $form): string {
        if (!$this->hidden) {
            $attributes = $this->set_standard_attributes($this->attributes);
            return "<select " . $attributes . ">" . $this->get_options($form) . "</select>\n";
        } else {
            return parent::get_html($form);
        }
    }

    public function get_options(form $form): string {
        $html = '';
        $class = $this->link_module;
        $obj = schema::getFromClass($class);

        if (!$this->options->order) {
            $this->options->order = $obj->primary_key;
        }
        $options = $class::get_all($this->options);
        $html .= '<option value="0">- Please Select -</option>';

        return $html . $options->reduce(fn (string $acc, model_interface $object): string => $acc . '<option value="' . $object->get_primary_key() . '" ' . ($this->is_selected($form, $object->get_primary_key()) ? 'selected="selected"' : '') . '>' . $this->get_object_title($object) . '</option>', '');
    }

    protected function is_selected(form $form, int $id): bool {
        return $this->get_value($form) == $id;
    }

    protected function get_object_title(model_interface $object): string {
        $fields = $this->link_field;
        if (is_array($fields)) {
            $parts = [];
            foreach ($fields as $part) {
                if (strpos($part, '.')) {
                    $part = explode('.', $part);
                    /** @psalm-suppress MixedPropertyFetch */
                    $parts[] = (string) $object->{$part[0]}->{$part[1]};
                } else {
                    $parts[] = (string) $object->$part;
                }
            }
            $title = implode(' - ', $parts);
        } else {
            $title = (string) $object->$fields;
        }
        return $title;
    }

    public static function sanitise_from_db(string $value): mixed {
        return (int) $value;
    }
}
