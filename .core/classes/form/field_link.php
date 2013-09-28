<?php
namespace form;
class field_link extends field {
    /** @var int|string */
    public $link_module;
    /** @var int|string */
    public $link_field;

    public $options = array();

    public function __construct($title, $options = array()) {
        parent::__construct($title, $options);
        $this->attributes['type'] = 'number';
    }

    public function  get_cms_list_wrapper($value, $object_class, $id) {
        $class = (is_numeric($this->link_module) ? \core::get_class_from_mid($this->link_module) : $this->link_module);
        $field_name = (is_numeric($this->link_field) ? \core::get_field_from_fid($this->link_field)->field_name : $this->link_field);
        $object = new $class();
        /** @var \table $object */
        $object->do_retrieve_from_id(array($field_name, $object->table_key), $value);
        return (isset($object->{$object->table_key}) && $object->{$object->table_key} ? $object->$field_name : '-');
    }

    public function get_database_create_query() {
        return 'int(6)';
    }

    public function get_html() {
        if (!$this->hidden) {
            return '<select ' . $this->get_attributes() . '>' . $this->get_options() . '</select>' . "\n";
        } else {
            return parent::get_html();
        }
    }

    public function get_options() {
        $html = '';
        if (is_numeric($this->link_module)) {
            $this->link_module = \core::get_class_from_mid($this->link_module);
        }
        /** @var $class \table_array */
        $class = $this->link_module;
        /** @var $object \table */
        $obj = new $class();
        if (is_numeric($this->link_field)) {
            $this->link_field = \core::get_field_from_fid($this->link_field)->field_name;
        }
        if (is_array($this->link_field)) {
            $fields = $this->link_field;
            $fields[] = $obj->table_key;
        } else {
            $fields = array($obj->table_key, $this->link_field);
        }
        $options = $class::get_all($fields, $this->options);
        if (!$this->required) {
            $html .= '<option value="0">- Please Select -</option>';
        }
        $title_fields = $this->link_field;
        $selected = isset($this->parent_form->{$this->field_name}) ? $this->parent_form->{$this->field_name} : 0;
        $options->iterate(function (\table $object) use (&$html, $title_fields, $selected) {
                if (is_array($title_fields)) {
                    $parts = array();
                    foreach ($title_fields as $part) {
                        $parts[] = $object->{str_replace('.', '_', $part)};
                    }
                    $title = implode(' - ', $parts);
                } else {
                    $title = $object->$title_fields;
                }
                $html .= '<option value="' . $object->{$object->table_key} . '" ' . ($object->{$object->table_key} == $selected ? 'selected="selected"' : '') . '>' . $title . '</option>';
            }
        );
        return $html;
    }

}
