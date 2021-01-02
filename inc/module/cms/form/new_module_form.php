<?php

namespace module\cms\form;

use classes\ajax;
use classes\db;
use classes\get;
use classes\table;
use form\form;

class new_module_form extends form {

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

    public function __construct() {
        parent::__construct([form::create('field_link', 'gid', ['link_module' => '\module\cms\model\_cms_group', 'link_field' => 'title', 'label' => 'Group']), form::create('field_string', 'primary_key', ['label' => 'Primary Key']), form::create('field_string', 'title', ['label' => 'Title']), form::create('field_string', 'title_label', ['label' => 'Title Label']), form::create('field_string', 'table_name', ['label' => 'Table Name']), form::create('field_string', 'namespace')->set_attr('label', 'Namespace <small>can be blank</small>')->set_attr('required', false), form::create('field_boolean', 'nestable', ['label' => 'Nestable']),]);
        $this->id = 'new_module';
    }

    public function do_submit(): bool {
        db::query('CREATE TABLE IF NOT EXISTS `' . $this->table_name . '` (
            `' . $this->primary_key . '` int(4) NOT NULL AUTO_INCREMENT,
            `parent_' . $this->primary_key . '` int(4) NOT NULL DEFAULT "0",
            `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `cms_created` timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",
            `live` tinyint(1) NOT NULL DEFAULT "0",
            `deleted` tinyint(1) NOT NULL DEFAULT "0",
            `position` int(6) NOT NULL DEFAULT "0",
            `' . get::fn($this->title_label) . '` varchar(255) NOT NULL,
            PRIMARY KEY (`' . $this->primary_key . '`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;');
        $mid = db::insert('_cms_module')->add_value('gid', $this->gid)->add_value('primary_key', $this->primary_key)->add_value('title', $this->title)->add_value('table_name', $this->table_name)->add_value('namespace', $this->namespace)->execute();
        $id_field = db::insert('_cms_field')->add_value('field_name', $this->primary_key)->add_value('title', 'ID')->add_value('type', 'int')->add_value('mid', $mid)->add_value('required', false)->add_value('list', false)->add_value('editable', false)->add_value('filter', false)->execute();
        db::insert('_cms_field')->add_value('field_name', 'parent_' . $this->primary_key)->add_value('title', 'Parent ID')->add_value('type', 'link')->add_value('mid', $mid)->add_value('link_module', $mid)->add_value('link_field', (int)$id_field + 2)->add_value('required', false)->add_value('list', false)->execute();
        db::insert('_cms_field')->add_value('field_name', get::fn($this->title_label))->add_value('title', $this->title_label)->add_value('type', 'string')->add_value('mid', $mid)->execute();
        $file = null;
        if (!$this->namespace) {
            if (!is_dir(root . '/inc/object/')) {
                mkdir(root . '/inc/object/');
            }
            if (!file_exists(root . '/inc/object/' . $this->table_name)) {
                $file = root . '/inc/object/' . $this->table_name;
            }
        } else {
            if (!is_dir(root . '/inc/module/')) {
                mkdir(root . '/inc/module/');
            }
            if (!is_dir(root . '/inc/module/' . $this->namespace)) {
                mkdir(root . '/inc/module/' . $this->namespace);
            }
            if (!is_dir(root . '/inc/module/' . $this->namespace . '/object/')) {
                mkdir(root . '/inc/module/' . $this->namespace . '/object');
            }
            if (!file_exists(root . '/inc/module/' . $this->namespace . '/object/' . $this->table_name)) {
                $file = root . '/inc/module/' . $this->namespace . '/object/' . $this->table_name;
            }
        }
        if ($file) {
            $class_name = ($this->namespace ? 'module\\' . $this->namespace . '\\' : '') . 'model\\' . $this->table_name;
            file_put_contents($file . '.php', '<?php
namespace ' . ($this->namespace ? 'module\\' . $this->namespace . '\\' : '') . 'object;

use classes\table;


class ' . $this->table_name . ' extends ' . (class_exists('\\\\' . $class_name) ? '\\\\' . $class_name : 'table') . ' {



    /** @var string */
    public $' . get::fn($this->title_label) . ';
    /** @var int */
    public $' . get::fn($this->primary_key) . ';

}');
        }
        table::rebuild_modules();
        ajax::add_script('window.location = window.location');
        return true;
    }
}
