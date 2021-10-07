<?php

namespace module\cms\form;

use classes\ajax;
use classes\attribute_callable;
use classes\session;
use classes\table;
use form\field;
use form\field_boolean;
use form\form;
use form\schema;
use html\node;
use module\cms\model\_cms_table_list;

class cms_filter_form extends form {

    public int $npp;

    public function __construct(public string $mid) {
        $class = schema::getFromClass($mid);
        $super_fields = $class->fields;
        $fields = [
            new \form\field_boolean('deleted', label: 'Show deleted?',required: false),
            new \form\field_select('npp', label: 'Number per page',options: [25 => 25, 50 => 50, 75 => 75, 100 => 100, 0 => 'All'],required: false),
            new \form\field_int('_mid', hidden: true,)
        ];
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
    }

    public static function do_clear_filter(): void {
        session::un_set('cms', 'filter', (string) $_REQUEST['_mid']);
        $cms_filter_form = new self((string) $_REQUEST['_mid']);
        $cms_filter_form->post_fields_text = '';
        $cms_filter_form->do_submit(true);
    }

    public function do_submit(bool $no_session = false): bool {
        if (!$no_session) {
            foreach ($this->fields as $field) {
                if ($field instanceof field_boolean && !$this->{$field->field_name}) {
                    session::un_set('cms', 'filter', $this->mid, $field->field_name);
                } else {
                    session::set($this->{$field->field_name}, 'cms', 'filter', $this->mid, $field->field_name);
                }
            }
        }
        $module = schema::getSchemas()[$this->mid];
        $list = new _cms_table_list($module, 1);
        ajax::update($list->get_table());
        return true;
    }

    public function get_submit(): string {
        $html = parent::get_submit();
        if (session::is_set('cms', 'filter', $this->mid)) {
            $html .= node::create('a.btn.btn-default', ['href' => '#', 'dataAjaxClick' => attribute_callable::create([cms_filter_form::class, 'do_clear_filter']), 'dataAjaxPost' => '{"_mid":"' . $this->mid . '"}', 'dataAjaxShroud' => '#filter_form'], 'Clear Filters');
        }
        return "<span>$html</span>";
    }

    public function get_html(): string {
        if (!empty($this->fields)) {
            return parent::get_html();
        }
        return '<span></span>';
    }
}
