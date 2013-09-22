<?php
/**
 * Class _cms_group
 */
namespace cms;
class _cms_group extends \table { use \table_trait;

    /**
     * @var string
     */
    public $table_key = 'gid';
    /**
     * @var int
     */
    public static $module_id = 19;


    /**
     * @param array $fields
     * @param array $options
     * @return _cms_group_array
     */
    public static function get_all(array $fields, array $options = array()) {
        return _cms_group_array::get_all($fields, $options);
    }
}

/**
 * Class _cms_group_array
 */
class _cms_group_array extends \table_array {

    /**
     * @param array $input
     */
    public function __construct($input = array()) {
        parent::__construct($input, 0, '_cms_group_iterator');
        $this->iterator = new _cms_group_iterator($input);
    }

    /* @return _cms_group */
    public function next() {
        return parent::next();
    }
}

/**
 * Class _cms_group_iterator
 */
class _cms_group_iterator extends \table_iterator {

    /* @return _cms_group */
    public function key() {
        return parent::key();
    }
}