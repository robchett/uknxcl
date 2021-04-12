<?php

namespace form;

use classes\icon;
use html\node;
use RuntimeException;

class field_file extends field {

    public array $attributes = ['type' => 'file'];
    public array $class = [];
    public bool $external = false;

    public function get_cms_list_wrapper($value, $object_class, $id): string {
        $this->attributes['href'] = $this->parent_form->get_download_path(flight::DOWNLOAD_IGC);
        return node::create('a.btn.btn-success', $this->attributes, icon::get('save'));
    }

    public function get_html_wrapper(): string {
        $html = '';
        $html .= $this->pre_text;

        if (!$this->hidden && isset($this->label) && !empty($this->label)) {
            $html .= node::create('label.control-label.col-md-' . $this->parent_form->bootstrap[0], ['for' => $this->field_name, 'id' => $this->field_name . '_wrapper'], $this->label);
        }
        $html .= node::create('div.col-md-' . $this->parent_form->bootstrap[1] . ' div.fileinput.fileinput-new.input-group', ['data-provides' => 'fileinput'], [node::create('div.form-control', ['data-trigger' => 'fileinput'], [icon::get('file', 'i', ['class' => ['fileinput-exists']]), node::create('span.fileinput-filename', []),]), node::create('span.input-group-addon.btn.btn-default.btn-file', [], [node::create('span.fileinput-exists', [], 'Change'), node::create('span.fileinput-new', [], 'Select File'), $this->get_html(),]), node::create('a.input-group-addon.btn.btn-default.fileinput-exists.fileremove', ['data-dismiss' => 'fileinput'], 'Remove')]);
        $html .= $this->post_text;
        return $html;
    }

    public function get_save_sql() {
        throw new RuntimeException('Can\t save this field type');
    }

    public function set_from_request() {
    }

    public function do_validate(&$error_array) {

    }
}
