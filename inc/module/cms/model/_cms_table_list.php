<?php

namespace module\cms\model;

use classes\ajax;
use classes\attribute_callable;
use classes\attribute_list;
use classes\get;
use classes\interfaces\model_interface;
use classes\paginate;
use classes\session;
use classes\table;
use classes\table_array;
use classes\tableOptions;
use core;
use form\schema;
use html\bootstrap\modal;
use html\node;
use module\cms\controller;
use module\cms\form\cms_filter_form;

class _cms_table_list
{

    protected schema $module;
    protected table_array $elements;
    /** @var array<string, scalar> */
    public array $where = [];
    protected int $page;
    protected int $npp;
    private bool $deleted;

    public function __construct(schema $module,  int $page)
    {
        $this->module = $module;
        $this->page = $page;
        $this->npp = (int) (session::is_set('cms', 'filter', $module->table_name, 'npp') ? session::get('cms', 'filter', $module->table_name, 'npp') : 25);
        $this->deleted = (bool) (session::is_set('cms', 'filter', $module->table_name, 'deleted') ? session::get('cms', 'filter', $module->table_name, 'deleted') : false);

        foreach ($module->fields as $field) {
            if (session::is_set('cms', 'filter', $module->table_name, $field->field_name) && session::get('cms', 'filter', $module->table_name, $field->field_name)) {
                $this->where[$field->field_name] = session::get('cms', 'filter', $module->table_name, $field->field_name);
            }
        }
        $this->elements = $this->get_elements();
    }

    public static function do_paginate(): void
    {
        $module = schema::getFromClass((string) $_REQUEST['_mid']);
        $object = new self($module, (int) $_REQUEST['value']);
        ajax::update($object->get_table());
    }

    public function get_table(): string
    {
        return node::create('div#inner', [], $this->get_list() . $this->get_delete_modal());
    }

    protected function get_elements(int $parent_id = 0): table_array
    {
        $options = new tableOptions(where_equals: $this->where + ['parent_' . $this->module->primary_key => $parent_id], order: $this->module->table_name . '.position');
        if ($this->npp && $parent_id === 0) {
            $options->limit = ($this->page - 1) * $this->npp . ',' . $this->npp;
        }
        return $this->module->object::get_all($options);
    }

    protected function get_list(): string
    {
        return $this->get_filters() . $this->get_list_inner() . node::create('div.container-fluid', [], $this->get_pagi());
    }

    public function get_filters(): string
    {
        $filter_form = new cms_filter_form($this->module->table_name);
        return node::create(
            'div.container-fluid div.panel.panel-default',
            [],
            node::create(
                'div.panel-heading h4.panel-title.clearfix',
                [],
                node::create('a.btn.btn-default', ['href' => '#filter_bar', 'dataToggle' => 'collapse'], 'Filters') .
                    node::create('div.pull-right', [], $this->get_pagi()),
            ) .
                node::create('div#filter_bar.panel-collapse.collapse div.panel-body', [], $filter_form->get_html())
        );
    }

    public function get_pagi(): string
    {
        $paginate = new paginate();
        $paginate->total = $this->elements->get_total_count();
        $paginate->npp = $this->npp;
        $paginate->page = $this->page;
        $paginate->base_url = '/cms/module/' . $this->module->table_name;
        $paginate->act = attribute_callable::create([_cms_table_list::class, 'do_paginate']);
        $paginate->post_data = ['_mid' => $this->module->table_name];
        return (string)$paginate;
    }

    protected function get_list_inner(): string
    {
        return node::create('div.container-fluid table.module_list.table.table-striped', [], $this->get_table_head() . $this->get_table_rows($this->elements));
    }

    public function get_table_head(): string
    {
        $nodes = node::create('col.btn-col') . node::create('col.btn-col') . node::create('col.btn-col2');
        foreach ($this->module->fields as $field) {
            if ($field->list) {
                $nodes .= node::create('col.' . get::__class_name($field));
            }
        };
        $nodes .= node::create('col.btn-col');
        $nodes = "<colgroup>{$nodes}<colgroup>";

        $inner = '';
        foreach ($this->module->fields as $field) {
            if ($field->list) {
                $inner .= node::create('th.' . get::__class_name($field) . '.header_' . $field->field_name . ($field->field_name == $this->module->primary_key ? '.primary' : ''), [], $field->label);
            }
        }

        return $nodes . node::create('thead', [], node::create('th.edit.btn-col') . node::create('th.live.btn-col', []) . node::create('th.position.btn-col2', []) . $inner . node::create('th.delete.btn-col'));
    }

    public function get_table_rows(table_array $elements, string $class = ''): string
    {
        return $elements->reduce(fn (string $acc, model_interface $obj): string => $acc .
            node::create('tr#' . get::__class_name($obj) . $obj->get_primary_key() . ($obj->deleted ? '.danger.deleted' : '') . $class . '.vertical-align', [], $obj->get_cms_list())
        ); 
    }

    protected function get_delete_modal(): string
    {
        core::$inline_script[] = <<<JS
        $("body").on('click', 'button.delete', function(e) {
            $("#delete, #undelete").data('ajaxPost', $(this).data('ajaxPost'));
        });
JS;

        return
            modal::create(
                'delete_modal',
                new attribute_list(class: ['delete_modal', 'modal', 'fade'], role: 'dialog', tabindex: '-1', ariaHidden: 'true'),
                node::create('button.close', ['dataDismiss' => 'modal'], '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>') . node::create('div.modal-title', [], 'Delete'),
                "<p>Are you sure you want to do this?<p>",
                node::create('button.btn.btn-default', ['dataDismiss' => 'modal'], 'Cancel') . node::create('button#delete.btn.btn-primary', ['dataDismiss' => 'modal', 'dataAjaxClick' => attribute_callable::create([controller::class, 'do_delete'])], 'Delete')
            ) .
            modal::create(
                'undelete_modal',
                new attribute_list(class: ['undelete_modal', 'modal', 'fade'], role: 'dialog', tabindex: '-1', ariaHidden: 'true'),
                node::create('button.close', ['dataDismiss' => 'modal'], '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>') . node::create('div.modal-title', [], 'Un Delete'),
                "<p>Are you sure you want to do this?<p>",
                node::create('button.btn.btn-default', ['dataDismiss' => 'modal'], 'Cancel') . node::create('button#undelete.btn.btn-primary', ['dataDismiss' => 'modal', 'dataAjaxClick' => attribute_callable::create([controller::class, 'do_undelete'])], 'Un-delete')
            ) .
            modal::create(
                'true_delete_modal',
                new attribute_list(class: ['true_delete_modal', 'modal', 'fade'], role: 'dialog', tabindex: '-1', ariaHidden: 'true'),
                node::create('button.close', ['dataDismiss' => 'modal'], '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>') . node::create('div.modal-title', [], 'Completely Delete'),
                node::create('h2', [], 'This cannot be reversed!') . "<p>Are you sure you want to do this?<p>",
                node::create('button.btn.btn-default', ['dataDismiss' => 'modal'], 'Cancel') . node::create('button#undelete.btn.btn-primary', ['dataDismiss' => 'modal', 'dataAjaxClick' => attribute_callable::create([controller::class, 'do_delete'])], 'Delete')
            );
    }
}
