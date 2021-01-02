<?php

namespace module\cms\model;

use classes\ajax;
use classes\attribute_callable;
use classes\get;
use classes\paginate;
use classes\session;
use classes\table;
use classes\table_array;
use core;
use html\bootstrap\modal;
use html\node;
use module\cms\controller;
use module\cms\form\cms_filter_form;
use module\cms\model\_cms_module as __cms_module;

class _cms_table_list {

    public array $where = [];
    public string $order = 'position';
    protected _cms_module $module;
    protected int $page;
    protected int $npp;
    protected table $class;
    protected table_array $elements;
    private string $class_name;
    /**
     * @var array|int[]|mixed
     */
    private $allowed_keys;
    /**
     * @var false|mixed
     */
    private $deleted;

    public function __construct(__cms_module $module, $page) {
        $this->module = $module;
        $this->page = $page;
        $this->npp = session::is_set('cms', 'filter', $module->mid, 'npp') ? session::get('cms', 'filter', $module->mid, 'npp') : 25;
        $this->deleted = session::is_set('cms', 'filter', $module->mid, 'deleted') ? session::get('cms', 'filter', $module->mid, 'deleted') : false;
        $this->allowed_keys = ['' => 0] + (session::is_set('cms', 'expand', $module->mid) ? session::get('cms', 'expand', $module->mid) : []);
        $this->where = [];

        if ($module->order && preg_match('/[a-zA-Z0-9,\s]+/', $module->order)) {
            $this->order = $module->order;
        }

        $class = $this->module->get_class_name();
        $this->class_name = $class;
        $this->class = new $class();

        foreach ($module->get_class()->get_fields() as $field) {
            if (session::is_set('cms', 'filter', $module->mid, $field->field_name) && session::get('cms', 'filter', $module->mid, $field->field_name)) {
                $this->where[$field->field_name] = session::get('cms', 'filter', $module->mid, $field->field_name);
            }
        }
    }

    public static function do_paginate() {
        $module = new __cms_module();
        $module->do_retrieve_from_id([], $_REQUEST['_mid']);
        $object = new static($module, $_REQUEST['value']);
        ajax::update($object->get_table());
    }

    public function get_table(): string {
        if (!isset($this->elements)) {
            $this->elements = $this->get_elements();
        }
        return node::create('div#inner', [], array_merge($this->get_list(), $this->get_delete_modal()));
    }

    protected function get_elements($parent_id = 0) {
        $class = $this->class_name;
        /** @var table $obj */
        $obj = new $class;
        $options = ['where_equals' => $this->where + ['parent_' . $obj->get_primary_key_name() => $parent_id], 'order' => $this->order ?: 'position',];
        if ($this->npp && $parent_id === 0) {
            $options['limit'] = ($this->page - 1) * $this->npp . ',' . $this->npp;
        }
        $class::$retrieve_unlive = true;
        if ($this->deleted) {
            $class::$retrieve_deleted = true;
        }
        return $class::get_all(['*', '(SELECT COUNT(' . $obj->get_primary_key_name() . ') FROM ' . get::__class_name($obj) . ' t WHERE t.parent_' . $obj->get_primary_key_name() . ' = ' . get::__class_name($obj) . '.' . $obj->get_primary_key_name() . ' LIMIT 1) AS _has_child'], $options);
    }

    /**
     * @return array
     */
    protected function get_list(): array {
        return [$this->get_filters(), $this->get_list_inner(), node::create('div.container-fluid', [], $this->get_pagi()),];
    }

    /**
     * @return string
     */
    public function get_filters(): string {
        $filter_form = new cms_filter_form($this->module->mid);
        return node::create('div.container-fluid div.panel.panel-default', [], [node::create('div.panel-heading h4.panel-title.clearfix', [], [node::create('a.btn.btn-default', ['href' => '#filter_bar', 'data-toggle' => 'collapse'], 'Filters'), node::create('div.pull-right', [], $this->get_pagi()),]), node::create('div#filter_bar.panel-collapse.collapse div.panel-body', [], $filter_form->get_html())]);
    }

    /**
     * @return string
     */
    public function get_pagi(): string {
        $paginate = new paginate();
        $paginate->total = $this->elements->get_total_count();
        $paginate->npp = $this->npp;
        $paginate->page = $this->page;
        $paginate->base_url = '/cms/module/' . $this->module->mid;
        $paginate->act = attribute_callable::create([\module\cms\model\_cms_table_list::class, 'do_paginate']);
        $paginate->post_data = ['_mid' => $this->module->mid];
        return (string)$paginate;
    }

    protected function get_list_inner(): string {
        return $this->module->get_cms_pre_list() . node::create('div.container-fluid table.module_list.table.table-striped', [], [...$this->get_table_head(), $this->get_table_rows($this->elements)]) . $this->module->get_cms_post_list();
    }

    /**
     * @return array
     */
    public function get_table_head(): array {
        $obj = $this->class;

        $nodes = [];
        $nodes[] = node::create('col.btn-col');
        $nodes[] = node::create('col.btn-col');
        if ($this->module->nestable) {
            $nodes[] = node::create('col.btn-col');
        }
        $nodes[] = node::create('col.btn-col2');
        $obj->get_fields()->iterate(function ($field) use (&$nodes) {
            if ($field->list) {
                $nodes[] = node::create('col.' . get::__class_name($field));
            }
        });
        $nodes[] = node::create('col.btn-col');
        $nodes = ["<colgroup>{$nodes}<colgroup>"];


        $nodes[] = node::create('thead', [], node::create('th.edit.btn-col') . node::create('th.live.btn-col', []) . ($this->module->nestable ? node::create('th.expand.btn-col', []) : '') . node::create('th.position.btn-col2', []) . $obj->get_fields()->iterate_return(function ($field) use ($obj) {
                if ($field->list) {
                    return node::create('th.' . get::__class_name($field) . '.header_' . $field->field_name . ($field->field_name == $obj->get_primary_key_name() ? '.primary' : ''), [], $field->title);
                }
                return '';
            }) . node::create('th.delete.btn-col'));
        return $nodes;
    }

    /**
     * @param $elements
     * @param string $class
     *
     * @return string
     */
    public function get_table_rows($elements, string $class = ''): string {
        $keys = $this->allowed_keys;
        /**
         * @return string
         * @var table $obj
         */
        return $elements->iterate_return(function (table $obj) use ($keys, $class) {
            if ($obj->_has_child && in_array($obj->get_primary_key(), $keys)) {
                $obj->_is_expanded = true;
                $children = $this->get_elements($obj->get_primary_key());
            } else {
                $obj->_is_expanded = false;
                $children = false;
            }
            return node::create('tr#' . get::__class_name($obj) . $obj->get_primary_key() . ($obj->deleted ? '.danger.deleted' : '') . $class . '.vertical-align', [], $obj->get_cms_list()) . ($children ? $this->get_table_rows($children, '.active') : '');
        });
    }

    protected function get_delete_modal(): array {
        core::$inline_script[] = <<<JS
        $("body").on('click', 'button.delete', function(e) {
            $("#delete, #undelete").data('ajax-post', $(this).data('ajax-post'));
        });
JS;

        return [
            modal::create('delete_modal', ['class' => ['delete_modal', 'modal', 'fade'], 'role' => 'dialog', 'tabindex' => -1, 'aria-hidden' => true], [node::create('button.close', ['data-dismiss' => 'modal'], '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>'), node::create('div.modal-title', [], 'Delete')], ["<p>Are you sure you want to do this?<p>"], [node::create('button.btn.btn-default', ['data-dismiss' => 'modal'], 'Cancel') . node::create('button#delete.btn.btn-primary', ['data-dismiss' => 'modal', 'data-ajax-click' => attribute_callable::create([controller::class, 'do_delete'])], 'Delete'), modal::create('undelete_modal', ['class' => ['undelete_modal', 'modal', 'fade'], 'role' => 'dialog', 'tabindex' => -1, 'aria-hidden' => true], [node::create('button.close', ['data-dismiss' => 'modal'], '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>'), node::create('div.modal-title', [], 'Un Delete')], ["<p>Are you sure you want to do this?<p>"], [node::create('button.btn.btn-default', ['data-dismiss' => 'modal'], 'Cancel') . node::create('button#undelete.btn.btn-primary', ['data-dismiss' => 'modal', 'data-ajax-click' => 'cms:do_undelete'], 'Un-delete')]), modal::create('true_delete_modal', ['class' => ['true_delete_modal', 'modal', 'fade'], 'role' => 'dialog', 'tabindex' => -1, 'aria-hidden' => true], [node::create('button.close', ['data-dismiss' => 'modal'], '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>'), node::create('div.modal-title', [], 'Completely Delete')], [node::create('h2', [], 'This cannot be reversed!'), "<p>Are you sure you want to do this?<p>",], [node::create('button.btn.btn-default', ['data-dismiss' => 'modal'], 'Cancel') . node::create('button#undelete.btn.btn-primary', ['data-dismiss' => 'modal', 'data-ajax-click' => 'cms:do_delete'], 'Delete')])];
    }

}