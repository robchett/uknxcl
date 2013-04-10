<?php

class field_link extends field {

    public $options = array();

    public function __construct($title, $options = array()) {
        parent::__construct($title, $options);
        $this->attributes['type'] = 'number';
    }

    public function get_database_create_query(){
        return 'int(6)';
    }

    public function get_html() {
        if(!$this->hidden) {
            return '<select ' . $this->get_attributes() . '>' . $this->get_options() . '</select>' . "\n";
        } else {
            return parent::get_html();
        }
    }

    public function get_options() {
        $html = '';
        $class = core::get_class_from_mid($this->link_mid);
        $field_name = core::get_field_from_fid($this->link_fid);
        $obj = new $class();
        $options = $class::get_all(array($field_name,$obj->table_key), $this->options);
        if(!$this->required) {
            $html .= '<option value="0">- Please Select -</option>';
        }
        $selected = isset($this->parent_form->{$this->field_name}) ? $this->parent_form->{$this->field_name} : 0 ;
        $options->iterate(function(table $object) use (&$html, $field_name, $selected) {
            $html .= '<option value="' . $object->{$object->table_key} . '" ' . ($object->{$object->table_key} == $selected ? 'selected="selected"' : '') . '>' . $object->{$field_name}  . '</option>';
        });
        return $html;
    }

    public function  get_cms_list_wrapper($value, $object_class ,$id) {
        $class = core::get_class_from_mid($this->link_mid);
        $field_name = core::get_field_from_fid($this->link_fid);
        $object = new $class();
        $object->do_retrieve_from_id(array($field_name, $object->table_key), $value);
        return (isset($object->{$object->table_key}) && $object->{$object->table_key} ?$object->$field_name : '-');
    }

}
