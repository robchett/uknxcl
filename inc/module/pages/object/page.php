<?php
namespace pages;
class page extends \table {
    use \table_trait;

    public static $module_id = 1;
    public $nav_title;
    public $pid;
    public $table_key = 'pid';
    public $body;
    public $module_name = '';
    public $title;

    /**
     * @param array $fields
     * @param array $options
     * @return page_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return page_array::get_all($fields, $options);
    }

    /**
     * @return string
     */
    public function get_url() {
        if (!empty($this->module_name)) {
            return '/' . $this->module_name;
        } else {
            return '/' . $this->pid . '/' . \get::fn($this->title);
        }
    }
}

/**
 * Class page_array
 * @package pages
 */
class page_array extends \table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, '\pages\page_iterator');
        $this->iterator = new page_iterator($input);
    }

    /* @return page */
    public function next() {
        return parent::next();
    }

    /**
     *
     */
    protected function set_statics() {
        parent::set_statics();
    }
}

/**
 * Class page_iterator
 * @package pages
 */
class page_iterator extends \table_iterator {

    /* @return page */
    public function key() {
        return parent::key();
    }
}