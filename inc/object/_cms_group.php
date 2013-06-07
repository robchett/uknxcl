<?php 
class _cms_group extends table {
    public $table_key = 'gid';
    public static $module_id = 0;
    
    /* @return _cms_group_array */
    public static function get_all(array $fields, array $options = array()){
        return _cms_group_array::get_all($fields, $options);
    }
}

class _cms_group_array extends table_array {
    
    public function __construct($input = array()){
        parent::__construct($input,0,'_cms_group_iterator');
        $this->iterator = new _cms_group_iterator($input);
    }

    /* @return _cms_group */
    public function next(){
        return parent::next();
    }
    
    protected  function set_statics() {
        parent::set_statics();
    }
}

class _cms_group_iterator extends table_iterator {

    /* @return _cms_group */
    public function key(){
        return parent::key();
    }
}