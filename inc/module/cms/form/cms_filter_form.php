<?php

namespace module\cms\form;

use classes\ajax;
use classes\attribute_callable;
use classes\session;
use classes\table;
use form\field;
use form\field_boolean;
use form\form;
use html\node;
use module\cms\model\_cms_module;
use module\cms\model\_cms_table_list;

class cms_filter_form extends form {

    public array $bootstrap = [2, 10, 'form-horizontal'];

    public $npp;
    /**
     * @var int|mixed
     */
    private $_mid;

    public function __construct($mid = 0) {
        if (ajax && !$mid) {
            $mid = $_REQUEST['_mid'];
        }
        $class_name = table::get_class_from_mid($mid);
        /** @var table $class */
        $class = new $class_name;
        $super_fields = $class->get_fields();
        $fields = [form::create('field_boolean', 'deleted')->set_attr('label', 'Show deleted?')->set_attr('options', [25 => 25, 50 => 50, 75 => 75, 100 => 100, 0 => 'All'])->set_attr('required', false), form::create('field_select', 'npp')->set_attr('label', 'Number per page')->set_attr('options', [25 => 25, 50 => 50, 75 => 75, 100 => 100, 0 => 'All'])->set_attr('required', false), form::create('field_int', '_mid')->set_attr('hidden', true)];
        /** @var field $field */
        foreach ($super_fields as $field) {
            if ($field->filter) {
                $field->required = false;
                $fields[] = $field;
            }
        }
        parent::__construct($fields);
        /** @var field $field */
        foreach ($fields as $field) {
            if (session::is_set('cms', 'filter', $mid, $field->field_name)) {
                $this->{$field->field_name} = session::get('cms', 'filter', $mid, $field->field_name);
            }
        }
        $this->id = 'filter_form';
        $this->submit = 'Filter';
        $this->_mid = $mid;
    }

    public static function do_clear_filter() {
        session::un_set('cms', 'filter', $_REQUEST['_mid']);
        $cms_filter_form = new static();
        $cms_filter_form->post_fields_text = '';
        $cms_filter_form->do_submit(true);
    }

    public function do_submit($no_session = false): bool {
        if (!$no_session) {
            foreach ($this->fields as $field) {
                if ($field instanceof field_boolean && !$this->{$field->field_name}) {
                    session::un_set('cms', 'filter', $this->_mid, $field->field_name);
                } else {
                    session::set($this->{$field->field_name}, 'cms', 'filter', $this->_mid, $field->field_name);
                }
            }
        }
        $module = new _cms_module();
        $module->do_retrieve([], ['where_equals' => ['mid' => $this->_mid]]);
        $list = new _cms_table_list($module, 1);
        ajax::update($list->get_table());
        return true;
    }

    public function get_submit(): node {
        $html = parent::get_submit();
        if (session::is_set('cms', 'filter', $this->_mid)) {
            $html .= node::create('a.btn.btn-default', ['href' => '#', 'data-ajax-click' => attribute_callable::create([\module\cms\form\cms_filter_form::class, 'do_clear_filter']), 'data-ajax-post' => '{"_mid":"' . $this->_mid . '"}', 'data-ajax-shroud' => '#filter_form'], 'Clear Filters');
        }
        return "<span>{$html}<span>";
    }

    public function get_html(): string {
        if (!empty($this->fields)) {
            return parent::get_html();
        }
        return node::create('span');
    }
}
