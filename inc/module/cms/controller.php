<?php

namespace module\cms;

use classes\ajax;
use classes\db;
use classes\get;
use classes\interfaces\model_interface;
use classes\module;
use classes\push_state;
use classes\session;
use classes\table;
use classes\tableOptions;
use core;
use form\schema;
use html\node;
use module\cms\form\new_module_form;
use module\cms\model;
use module\cms\view\cms_view;
use module\cms\view\dashboard;
use module\cms\view\edit;
use module\cms\view\login;
use module\cms\view\module as ViewModule;

class controller extends module
{

    public static string $url_base = '/cms/';
    public schema $module;
    public bool $object = false;

    /** @param string[] $path */
    public function __construct(array $path)
    {
        core::$css = ['/css/cms.css'];
        core::$js = ['/js/cms.js'];
        if (!core::is_admin()) {
            $this->view_object = new login($this, schema::getFromClass('page'), false);
            return;
        }
        if (!isset($path[1]) || !isset(schema::getSchemas()[$path[2] ?? ''])) {
            $this->view_object = new dashboard($this, schema::getFromClass('page'), false);
            return;
        }

        $module = schema::getSchemas()[$path[2]];
        $this->npp = session::is_set('cms', $module->object, 'npp') ? (int) session::get('cms', $module->object, 'npp') : 25;
        $this->page = (int) ($path[4] ?? 1);

        if (isset($path[3]) && is_numeric($path[3])) {
            $class = $module->object;
            $this->view_object = new edit($this, $module, $class::getFromId((int) $path[3]));
        } else {
            $this->view_object = new ViewModule($this, $module, false);
        }
        parent::__construct($path);
    }

    public function get_inner(): string
    {
        $list = new model\_cms_table_list($this->module, $this->page);
        return $list->get_table();
    }

    public function get_main_nav(): string
    {
        $groups = schema::getGroups();
        $inner = '';
        foreach ($groups as $title => $modules) {
            $moduleList = '';
            foreach ($modules as $moduleTitle => $module) {
                $srow = schema::getSchemas()[$module];
                $moduleList .= node::create('li a', ['href' => '/cms/module/' . $srow->table_name], $moduleTitle);
            };
            $inner .= node::create('li', [], node::create('a.dropdown-toggle', ['dataToggle' => 'dropdown'], $title . node::create('span.caret', [])) .  node::create('ul.dropdown-menu', ['role' => 'menu'], $moduleList));
        }
        return '
        <nav id="nav" class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <ul class="nav navbar-nav">
                    <li><a class="navbar-brand" href="/cms">CMS</a></li>
                    ' . $inner . '
                </ul>
            </div>
        </nav>';
    }

    public static function do_undelete(): void
    {
        $module = schema::getSchemas()[(string) $_REQUEST['mid']];
        $module->object::do_save([$module->primary_key => $_REQUEST['id'], 'deleted' => false]);
        $list = new model\_cms_table_list($module, 1);
        ajax::update($list->get_table());
    }

    public static function do_delete(): void
    {
        $module = schema::getSchemas()[(string) $_REQUEST['mid']];
        if ($object = $module->object::getFromId((int) $_REQUEST['id'])) {
            if ($object->deleted) {
                db::delete($module->table_name)->filter($object->get_primary_key_name() . '= :id', ['id' => (int) $_REQUEST['id']])->execute();
            } else {
                $object->deleted = true;
                $module->object::do_save([$module->primary_key => $_REQUEST['id'], 'deleted' => true]);
            }
        }
        $list = new model\_cms_table_list($module, 1);
        ajax::update($list->get_table());
    }
}
