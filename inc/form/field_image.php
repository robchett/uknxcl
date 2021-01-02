<?php

namespace form;

use classes\db;
use classes\table;
use classes\table_array;
use html\node;
use model\image_size;

class field_image extends field_file {

    protected static $image_sizes;

    public function get_image_edit_link(): string {
        $count = db::count('image_size', 'isid')->filter_field('fid', $this->fid)->execute();
        return node::create('a', ['href' => '/cms/module/' . image_size::get_module_id() . '/!/fid/' . $this->fid], (int)$count . ' image sizes');
    }

    public function get_cms_list_wrapper($value, $object_class, $id): string {
        return $this->get_default_image($id);
    }

    public function get_default_image($id, $options = []): string {
        $image_size = new image_size();
        $image_size->do_retrieve([], $options);
        if ($image_size->get_primary_key()) {
            $file = '/uploads/' . $this->parent_form->get_table_class() . '/' . $this->fid . '/' . $id . '_' . $image_size->reference . '.' . $image_size->get_format();
            if (file_exists(root . $file)) {
                return node::create('img', ['src' => $file]);
            }
        }
        return "<span>No Image<span>";
    }

    /**
     * @param $size
     *
     * @return bool
     */
    public function get_image_size($size): bool {
        foreach ($this->get_image_sizes() as $image_size) {
            if ($image_size->size == $size) {
                return $image_size;
            }
        }
        return false;
    }

    public function get_image_sizes(): table_array {
        if (!isset(self::$image_sizes)) {
            self::$image_sizes = image_size::get_all([]);
        }
        $image_sizes = new table_array();
        foreach (self::$image_sizes as $image_size) {
            if ($image_size->fid == $this->fid) {
                $image_sizes[] = $image_size;
            }
        }
        return $image_sizes;
    }

    public function get_html_wrapper(): string {
        $html = '';
        $html .= $this->pre_text;

        if (!$this->hidden && isset($this->label) && !empty($this->label)) {
            $html .= node::create('label.control-label.col-md-' . $this->parent_form->bootstrap[0], ['for' => $this->field_name, 'id' => $this->field_name . '_wrapper'], $this->label);
        }

        $parent = $this->parent_form;
        if ($parent instanceof table) {
            $primary = $parent->get_primary_key();
        } else {
            $primary = $parent->source_table->get_primary_key();
        }
        $image_size = new image_size();
        $options = ['where_equals' => ['fid' => $this->fid, 'default_edit' => true,]];
        $image_size->do_retrieve([], $options);
        $attributes = ['style' => ['width' => $image_size->max_width . 'px', 'height' => $image_size->max_height . 'px']];
        $html .= node::create('div.fileinput.fileinput-new.input_group.col-md-' . $this->parent_form->bootstrap[1], ['data-provides' => 'fileinput'], [node::create('div.fileinput-new.thumbnail', $attributes, $this->get_default_image($primary, $options)), node::create('div.fileinput-preview.fileinput-exists.thumbnail', $attributes), node::create('div', [], [node::create('span.btn.btn-default.btn-file', [], [node::create('span.fileinput-new', [], 'Select File'), node::create('span.fileinput-exists', [], 'Change'), $this->get_html(),]), node::create('a.btn.btn-default.fileinput-exists', ['data-dismiss' => 'fileinput'], 'Remove')])]);
        $html .= $this->post_text;
        return $html;
    }
}
 