<?php

namespace module\cms\model;

use classes\jquery;
use classes\table;
use core;
use Exception;
use form\field;
use form\field_collection;
use html\node;
use JetBrains\PhpStorm\NoReturn;
use module\cms\form\add_field_form;
use module\cms\form\cms_change_group_form;
use module\cms\form\edit_field_form;
use module\cms\form\edit_module_form;


class _cms_module extends table {


    public static array $default_fields = ['mid', 'primary_key', 'namespace', 'table_name', 'nestable'];
    /** @var field_collection */
    public field_collection $_field_elements;
    public string $name;
    public string $primary_key;
    public string $table_name;
    public string $title;
    public int $gid;
    public int $mid;
    public $nestable;
    public $order;
    public $_cms_group;
    public $namespace;

    public function __construct($fields = [], $id = 0) {
        parent::__construct($fields, $id);
        $this->_field_elements = new field_collection();
    }

    public static function create($title, $table_name, $primary_key, $group, $namespace = ''): _cms_module {
        $module = new _cms_module();
        $module->do_retrieve(['title'], ['where_equals' => ['table_name' => $table_name]]);
        if (!$module->get_primary_key()) {
            $module->title = $title;
            $module->table_name = $table_name;
            $module->primary_key = $primary_key;
            $module->gid = $group;
            $module->namespace = $namespace;
            $module->do_save();
        } else {
            throw new Exception('Module ' . $title . ' already exists.');
        }
        return $module;
    }

    public function get_primary_key_name(): string {
        return 'mid';
    }

    /**
     *
     */
    public function get_cms_change_group() {
        $form = new cms_change_group_form();
        $form->mid = $_REQUEST['mid'];

        jquery::colorbox(['html' => (string)$form->get_html()]);
    }

    /**
     * @return string
     */
    public function get_cms_edit_module(): string {
        $form = new edit_module_form();
        $form->set_from_object($this, false);
        return node::create('div.panel.panel-body', [], $form->get_html());
    }

    public function get_fields_list(): string {

        $obj = $this->get_class();

        return node::create('table#module_def.table.table-striped.', [], node::create('thead', [], "<th>Live</th><th>Edit</th><th>Field id</th><th>Pos</th><th>Title</th><th>Database Title</th><th>Type</th><th>List</th><th>Required</th><th>Filter<th>" . node::create('th', [])) . $obj->get_fields()->iterate_return(function (field $field) use ($obj) {
                $field->parent_form = $obj;
                return (node::create('tr.vertical-align', [], $field->get_cms_admin_edit()));
            }));
    }

    public function get_class(): table {
        $class = $this->get_class_name();
        return new $class();
    }

    public function get_class_name(): string {
        if ($this->namespace) {
            return '\\module\\' . $this->namespace . '\\model\\' . $this->table_name;
        } else {
            return '\\model\\' . $this->table_name;
        }
    }

    /**
     * @return string
     */
    #[NoReturn]
    public function get_edit_field_form(): string {
        $form = new edit_field_form();
        $form->mid = $this->mid;
        $field = new _cms_field([], $_REQUEST['fid']);
        $form->set_from_object($field, false);
        die(node::create('div.modal-header', [], 'Add new field') . node::create('div.modal-body', [], $form->get_html()) . '<script>' . implode("\n", core::$inline_script) . '</script>');
    }

    /**
     * @return string
     */
    #[NoReturn]
    public function get_new_field_form(): string {
        $form = new add_field_form();
        $form->mid = $_REQUEST['mid'];
        die(node::create('div.modal-header', [], 'Add new field') . node::create('div.modal-body', [], $form->get_html()) . '<script>' . implode("\n", core::$inline_script) . '</script>');
    }
}
