<?php

class cms_change_group_form extends form {

    public function __construct() {
        $fields = array(
            form::create('field_int', 'mid')
                ->set_attr('hidden', true),
            form::create('field_link', 'gid')
                ->set_attr('label', 'Group')
                ->set_attr('link_module', 19)
                ->set_attr('link_field', 123),
        );
        parent::__construct($fields);
    }

    public function do_submit() {
        if(parent::do_submit()) {
            $module = new _cms_modules();
            $module->do_retrieve_from_id(array('mid','title'), $this->mid);
            $module->gid = $this->gid;
            $module->do_save();
            ajax::add_script('$.fn.colorbox.close()');
        }
    }

}
