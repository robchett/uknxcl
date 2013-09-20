<?php
namespace cms;
use form\field_string;
use form\form;

class new_module_form extends form {

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
                form::create('field_link', 'gid', array('link_module' => 'cms\_cms_group', 'link_field' => 'title', 'label' => 'Group')),
                new field_string('primary_key', array('label' => 'Primary Key')),
                new field_string('title', array('label' => 'Title')),
                new field_string('table_name', array('label' => 'Table Name')),
            ]
        );
    }

    public function do_submit() {
        if (parent::do_submit()) {
            \db::query('CREATE TABLE IF NOT EXISTS `' . $this->table_name . '` (
            `' . $this->primary_key . '` int(4) NOT NULL AUTO_INCREMENT,
            `parent_' . $this->primary_key . '` int(4) NOT NULL DEFAULT "0",
            `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `cms_created` timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",
            `live` tinyint(1) NOT NULL DEFAULT "0",
            `deleted` tinyint(1) NOT NULL DEFAULT "0",
            PRIMARY KEY (`' . $this->primary_key . '`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;'
            );
            \db::query('INSERT INTO _cms_modules SET gid=:gid, primary_key=:pkey, title=:title, `table_name`=:tname, live=1, deleted=0', array(
                    'gid' => $this->gid,
                    'pkey' => $this->primary_key,
                    'title' => $this->title,
                    'tname' => $this->table_name,
                )
            );
            $mid = \db::insert_id();
            \db::query('INSERT INTO _cms_fields SET field_name=:field_name, title=\'ID\', `type`=\'int\', mid=:mid, list=0,required=0', array(
                    'mid' => $mid,
                    'field_name' => $this->primary_key
                )
            );
            \db::query('INSERT INTO _cms_fields SET field_name=:field_name, title=\'Parent ID\', `type`=\'int\', mid=:mid, list=0,required=0', array(
                    'mid' => $mid,
                    'field_name' => 'parent_' . $this->primary_key
                )
            );
        }
        \ajax::add_script('window.location = window.location');
    }
}
