<?php

namespace module\cms\form;

use classes\db;
use classes\table;
use form\form;
use module\cms\model\_cms_module;

class edit_module_form extends form {

    public $namespace;
    public $nestable;

    /** @var string */
    public string $table_name;
    /** @var string */
    public string $title;
    /** @var string */
    public string $primary_key;
    /** @var int */
    public int $gid;
    public $title_label;
    private $mid;

    public function __construct() {
        parent::__construct([form::create('field_link', 'gid', ['link_module' => '\module\cms\model\_cms_group', 'link_field' => 'title', 'label' => 'Group']), form::create('field_string', 'primary_key', ['label' => 'Primary Key']), form::create('field_string', 'title', ['label' => 'Title']), form::create('field_string', 'table_name', ['label' => 'Table Name']), form::create('field_string', 'namespace', ['label' => 'Namespace <small>can be blank</small>', 'required' => false]), form::create('field_boolean', 'nestable', ['label' => 'Nestable']), form::create('field_int', 'mid', ['hidden' => true]),]);
        $this->id = 'module_edit';
    }

    public function do_submit(): bool {
        $module = new _cms_module([], $this->mid);

        if ($module->table_name != $this->table_name) {
            db::rename_table($module->table_name, $this->table_name);
        }
        if ($module->primary_key != $this->primary_key) {
            db::rename_column($this->table_name, $module->primary_key, $this->primary_key);
        }
        table::rebuild_modules();
        $module->set_from_request();
        return $module->do_save();
    }
}
