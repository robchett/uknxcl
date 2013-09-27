<?php
namespace cms;
class _cms_modules extends \table { use \table_trait;

    public $namespace;
    public $primary_key;
    public $table_key = 'mid';
    public static $module_id = 20;
    public $table_name;
    public $title;

    /**
     * @param array $fields
     * @param array $options
     * @return _cms_modules_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return _cms_modules_array::get_all($fields, $options);
    }

    public function get_class_name() {
        return $this->namespace . '\\' . $this->table_name;
    }

    /** @return \table */
    public function get_class() {
        $class = $this->get_class_name();
        return new $class();
    }

    /**
     *
     */
    public function get_cms_change_group() {
        $form = new cms_change_group_form();
        $form->mid = $_REQUEST['mid'];

        \jquery::colorbox(array('html' => $form->get_html()->get()));
    }
}

/**
 * Class _cms_modules_array
 * @package cms
 */
class _cms_modules_array extends \table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, '\cms\_cms_modules_iterator');
        $this->iterator = new _cms_modules_iterator($input);
    }

    /* @return _cms_modules */
    public function next() {
        return parent::next();
    }
}

/**
 * Class _cms_modules_iterator
 * @package cms
 */
class _cms_modules_iterator extends \table_iterator {

    /* @return _cms_modules */
    public function key() {
        return parent::key();
    }
}