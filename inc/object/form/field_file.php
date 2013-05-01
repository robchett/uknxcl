<?php

class field_file extends field {
    public $external = false;

    public function get_html() {
        $html = '';
        $html .= '<a id="' . $this->field_name . '_wrapper" data-click="file_upload_' . $this->field_name . '" class="file_holder"><span class="icon">+</span><p class="text">Drag here to upload</p><input name="file" id="' . $this->field_name . '"  type="file"/></a>' . "\n";
        if (isset($this->parent_form->{$this->field_name})) {
            $path = pathinfo($this->parent_form->{$this->field_name});
            $html .= '<p><a href="' . $this->parent_form->{$this->field_name} . '" target="blank">' . $path['filename'] . '</a></p>';
        }
        return $html;

    }

    public function get_cms_list_wrapper($value, $object_class, $id) {
        if (isset($this->parent_form->{$this->field_name}) && !empty($this->parent_form->{$this->field_name})) {
            $this->attributes['href'] = $this->parent_form->{$this->field_name};
            return html_node::create('a.button', 'Download', $this->attributes)->get();
        } else {
            return html_node::create('span', 'no file')->get();
        }
    }

    public function get_save_sql(&$sql_array, &$parameters) {
    }

    public function set_from_request() {
    }

    public function do_validate(&$error_array) {

    }

}
