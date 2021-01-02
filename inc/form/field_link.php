<?php

namespace form;

use classes\collection;
use classes\get;
use classes\table;
use module\cms\model\_cms_field;

class field_link extends field {

    public int|string $link_module;
    public int|string|array $link_field;
    public int|string|array $value = '';

    public $order;

    public array $options = [];

    public function __construct($title, $options = []) {
        parent::__construct($title, $options);
        $this->attributes['type'] = 'number';
    }

    public function get_cms_list_wrapper($value, $object_class, $id): string {
        $class = (is_numeric($this->link_module) ? table::get_class_from_mid($this->link_module) : $this->link_module);
        $field_name = (is_numeric($this->link_field) && $this->link_field) ? _cms_field::get_field_from_fid($this->link_field)->field_name : ($this->link_field ?: 'title');
        $object = new $class();
        /** @var table $object */
        $object->do_retrieve_from_id([$field_name, $object->get_primary_key_name()], $value);
        return (isset($object->{$object->get_primary_key_name()}) && $object->{$object->get_primary_key_name()} ? $object->$field_name : '-');
    }

    public function get_database_create_query(): ?string {
        return 'int(6)';
    }

    public function get_html(): string {
        if (!$this->hidden) {
            $attributes = $this->attributes;
            $this->set_standard_attributes($attributes);
            return "<select " . static::get_attributes($attributes) . ">" . $this->get_options() . "</select>\n";
        } else {
            return parent::get_html();
        }
    }

    public function get_options(): string {
        $html = '';
        /** @var $class table */
        $class = $this->get_link_module();
        $fields = $this->get_link_fields();

        /** @var $object table */
        $obj = new $class();

        if (!isset($this->options['order'])) {
            $this->options['order'] = $obj->get_primary_key_name();
        }
        $options = $class::get_all(array_merge($fields, [$obj->get_primary_key_name(), 'parent_' . $obj->get_primary_key_name()]), $this->options);
        if (!$this->required) {
            $html .= '<option value="0">- Please Select -</option>';
        }

        $parents = new collection();
        $options->iterate(/* @var table $object */ function ($object) use (&$parents) {
            if (!$object->get_parent_primary_key()) {
                $object->_children = new collection();
                $parents[$object->get_primary_key()] = $object;
            } else if (isset($parents[$object->get_parent_primary_key()])) {
                $parents[$object->get_parent_primary_key()]->_children[] = $object;
            }
        });
        /* @var table $object */
        $parents->iterate(function ($object) use (&$html, $fields) {
            $html .= '<option value="' . $object->{$object->get_primary_key_name()} . '" ' . ($this->is_selected($object->{$object->get_primary_key_name()}) ? 'selected="selected"' : '') . '>' . $this->get_object_title($object, $fields) . '</option>';
            if ($object->_children->count()) {
                $object->_children->iterate(/* @var table $sub_object */ function ($sub_object) use (&$html, $fields) {
                    $html .= '<option value="' . $sub_object->{$sub_object->get_primary_key_name()} . '" ' . ($this->is_selected($sub_object->{$sub_object->get_primary_key_name()}) ? 'selected="selected"' : '') . '>' . $this->get_object_title($sub_object, $fields, 1) . '</option>';
                });
            }
        });
        return $html;
    }

    public function get_link_module(): int|string {
        if (is_numeric($this->link_module)) {
            $this->link_module = table::get_class_from_mid($this->link_module);
        }
        return $this->link_module;
    }

    public function get_link_fields(): array|int|string {
        if (is_numeric($this->link_field)) {
            if ($this->link_field) {
                $this->link_field = _cms_field::get_field_from_fid($this->link_field)->field_name;
            } else {
                $this->link_field = 'title';
            }
        }
        if (is_array($this->link_field)) {
            $fields = $this->link_field;
        } else {
            $fields = [$this->link_field];
        }
        return $fields;
    }

    protected function is_selected($id): bool {
        return $selected = (isset($this->parent_form->{$this->field_name}) ? $this->parent_form->{$this->field_name} : 0) == $id;
    }

    protected function get_object_title($object, $fields, $depth = 0): string {
        if (is_array($fields)) {
            $parts = [];
            foreach ($fields as $part) {
                if (strpos($part, '.')) {
                    $part = explode('.', $part);
                    $parts[] = $object->{$part[0]}->{$part[1]};
                } else {
                    $parts[] = $object->$part;
                }
            }
            $title = implode(' - ', $parts);
        } else {
            $title = $object->$fields;
        }
        for ($i = 0; $i < $depth; $i++) {
            $title = ' - ' . $title;
        }
        return $title;
    }

    /**
     * @return mixed
     */
    public function get_link_object(): mixed {
        $class = $this->get_link_module();
        return new $class;
    }

    public function get_link_mid(): int|string {
        if (is_numeric($this->link_module)) {
            return $this->link_module;
        }
        $class = $this->link_module;
        return $class::get_module_id();
    }
}
