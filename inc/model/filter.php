<?php

namespace model;

use classes\table;
use form\field_collection;


class filter extends table {


    public $link_fid;
    public $link_mid;
    public $parent_fid;
    public string $title;
    public $order;
    protected $field;
    private $options;
    private $field_name;
    private $field_name;
    private $field_name;
    private $field_name;
    private $field_name;
    private $field_name;
    private $field_name;
    private $field_name;
    private $fid;

    /**
     * @param array $fields
     * @param array $options
     * @return field_collection
     */
    public static function get_all(array $fields, array $options = []): field_collection {
        $array = new field_collection();
        $array->get_all(\model\filter::class, $fields, $options);
        return $array;
    }

    public function inner_field() {
        return $this->field;
    }

    /**
     * @param $field $field
     */
    public function set_field($field) {
        $this->field = $field;
    }

    public function __get($name) {
        return $this->field->$name;
    }

}
 