<?php
class page extends table {
    public static $module_id = 1;
    public $nav_title;
    /** @var int */
    public $pid;
    /** @var string */
    public $table_key = 'pid';
    /** @var string */
    public $body;
    /** @var string */
    public $module_name = '';
    /** @var string */
    public $title;

    /* @return page_array */
    public static function get_all(array $fields, array $options = array()) {
        return page_array::get_all($fields, $options);
    }

    public function get_url() {
        if (!empty($this->module_name)) {
            return '/' . $this->module_name;
        } else {
            return '/' . $this->pid . '/' . get::fn($this->title);
        }
    }
}

class page_array extends table_array {

    public function __construct($input = array()) {
        parent::__construct($input, 0, 'page_iterator');
        $this->iterator = new page_iterator($input);
    }

    /* @return page */
    public function next() {
        return parent::next();
    }

    protected function set_statics() {
        parent::set_statics();
    }
}

class page_iterator extends table_iterator {

    /* @return page */
    public function key() {
        return parent::key();
    }
}