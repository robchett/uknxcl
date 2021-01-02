<?php

namespace module\cms\form;

use classes\ajax;
use classes\db;
use classes\get;
use classes\table;
use core;
use form\field;
use form\form;
use module\cms\model\_cms_module;
use module\cms\model\field_type;

/**
 * @property mixed field_name
 */
class add_field_form extends form {

    public int $link_module = 0;
    public int $link_field = 0;
    public int $mid;
    public string $title;
    public int $type;

    public function __construct() {
        parent::__construct([
            form::create('field_string', 'title'),
            form::create('field_string', 'field_name'),
            form::create('field_link', 'type')->set_attr('link_module', '\module\cms\model\field_type')->set_attr('link_field', 'title'),
            form::create('field_link', 'link_module')->set_attr('link_module', '\module\cms\model\_cms_module')->set_attr('link_field', 'title'),
            form::create('field_link', 'link_field')->set_attr('link_module', '\module\cms\model\_cms_field')->set_attr('link_field', 'title'),
            form::create('field_int', 'mid')->set_attr('hidden', true)]);
        $this->id = 'add_field_form';
    }

    public function do_form_submit(): bool {
        if ($_REQUEST['type'] == 8 || $_REQUEST['type'] == 9) {
            $this->get_field_from_name('link_field')->required = true;
            $this->get_field_from_name('link_module')->required = true;
        }
        return parent::do_form_submit();
    }

    public function do_submit(): bool {
        $module = new _cms_module([], $this->mid);
        $field = new field_type([], $this->type);
        $type = '\form\field_' . $field->title;
        /** @var field $field_type */
        $field_type = new $type('');
        if ($inner = $field_type->get_database_create_query()) {
            db::query('ALTER TABLE ' . $module->table_name . ' ADD `' . $this->field_name . '` ' . $field_type->get_database_create_query(), [], 1);
        }

        if ($field->title == 'mlink') {
            $source_module = new _cms_module(['table_name', 'primary_key'], $this->link_module);
            db::create_table_join(get::__class_name($this), $source_module->table_name);
        }

        $res = db::select('_cms_field')->retrieve('MAX(position) AS pos')->filter_field('mid', $this->mid)->execute()->fetchObject();
        $insert = db::insert('_cms_field')->add_value('title', $this->title)->add_value('type', $field->title)->add_value('field_name', $this->field_name)->add_value('mid', $this->mid)->add_value('position', $res->pos + 1);
        if ($field->title == 'link' || $field->title == 'mlink') {
            $insert->add_value('link_module', $this->link_module)->add_value('link_field', $this->link_field);
        }
        $insert->execute();

        table::rebuild_modules();
        table::reset_module_fields($module->mid);

        ajax::update((string)$module->get_fields_list());
        ajax::update((string)$this->get_html());
        return true;
    }

    public function get_html(): string {
        core::$inline_script[] = '
            var $form = $("#add_field_form");
            $form.find("#add_field_form_field_link_module").hide();
            $form.find("#add_field_form_field_link_field").hide();
            $form.find("[name=type]").change(function() {
                if($(this).val() == 8 || $(this).val() == 9) {
                     $form.find("#add_field_form_field_link_module").show();
                     $form.find("#add_field_form_field_link_field").show();
                } else {
                     $form.find("#add_field_form_field_link_module").hide();
                     $form.find("#add_field_form_field_link_field").hide();
                }
            });
        ';
        return parent::get_html();
    }
}
