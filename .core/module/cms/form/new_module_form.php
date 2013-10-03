<?php
namespace core\module\cms\form;

use classes\ajax;
use core\classes\db;
use form\field_string;
use form\form;

abstract class new_module_form extends form {

    /** @var string */
    public $table_name;
    /** @var string */
    public $title;
    /** @var string */
    public $primary_key;
    /** @var int */
    public $gid;

    public function __construct() {
        parent::__construct(
            [
                form::create('field_link', 'gid', array('link_module' => '\module\cms\object\_cms_group', 'link_field' => 'title', 'label' => 'Group')),
                new field_string('primary_key', array('label' => 'Primary Key')),
                new field_string('title', array('label' => 'Title')),
                new field_string('table_name', array('label' => 'Table Name')),
            ]
        );
    }

    public function do_submit() {
        if (parent::do_submit()) {
            db::query('CREATE TABLE IF NOT EXISTS `' . $this->table_name . '` (
            `' . $this->primary_key . '` int(4) NOT NULL AUTO_INCREMENT,
            `parent_' . $this->primary_key . '` int(4) NOT NULL DEFAULT "0",
            `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `cms_created` timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",
            `live` tinyint(1) NOT NULL DEFAULT "0",
            `deleted` tinyint(1) NOT NULL DEFAULT "0",
            PRIMARY KEY (`' . $this->primary_key . '`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;'
            );
            $mid = db::insert('_cms_modules')
                ->add_value('gid', $this->gid)
                ->add_value('pkey', $this->primary_key)
                ->add_value('title', $this->title)
                ->add_value('tname', $this->table_name)
                ->execute();
            $id_field = db::insert('_cms_fields')
                ->add_value('field_name', $this->primary_key)
                ->add_value('title', 'ID')
                ->add_value('type', 'int')
                ->add_value('mid', $mid)
                ->execute();
            db::insert('_cms_fields')
                ->add_value('field_name', $this->primary_key)
                ->add_value('title', 'Parent ID')
                ->add_value('type', 'link')
                ->add_value('mid', $mid)
                ->add_value('link_module', $mid)
                ->add_value('link_field', $id_field)
                ->execute();
        }
        ajax::add_script('window.location = window.location');
    }
}
