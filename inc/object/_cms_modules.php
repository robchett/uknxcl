<?php
class _cms_modules extends table {
    public $table_key = 'mid';
    public static $module_id = 20;

    /* @return _cms_modules_array */
    public static function get_all(array $fields, array $options = array()) {
        return _cms_modules_array::get_all($fields, $options);
    }

    public function get_cms_change_group() {
        $form = new cms_change_group_form();
        $form->mid = $_REQUEST['mid'];

        return jquery::colorbox(array('html' => $form->get_html()->get()));
    }
}

class _cms_modules_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, '_cms_modules_iterator');
        $this->iterator = new _cms_modules_iterator($input);
    }

    /* @return _cms_modules */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class _cms_modules_iterator extends table_iterator {

    /* @return cms_module */
    public function key() {
        return parent::key();
    }
}