<?php

class field_button extends field {
    public function __construct($title = '', $options = array()) {
        parent::__construct($title, $options);
    }

    public function get_database_create_query() {
        return false;
    }

    public function get_cms_list_wrapper($value, $object_class, $id) {
        $this->attributes['data-ajax-click'] = $object_class . ':' . $this->field_name;
        $this->attributes['data-ajax-post'] = '{"id":' . $id . '}';
        $this->attributes['data-ajax-shroud'] = '#button' . $this->field_name. $id;
        return html_node::create('a#button' . $this->field_name. $id .'.button', $this->title, $this->attributes)->get();
    }

    public function get_save_sql(&$sql_array, &$parameters) {
    }
    public function get_html_wrapper() {
        return false;
    }
}
