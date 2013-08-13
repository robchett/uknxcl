<?php 
class comp_type extends table {
    public $table_key = 'ctid';
    public static $module_id = '23';
    
    /* @return comp_type_array */
    public static function get_all(array $fields, array $options = array()){
        return comp_type_array::get_all($fields, $options);
    }
}

class comp_type_array extends table_array {
    
    public function __construct($input = array()){
        parent::__construct($input,0,'comp_type_iterator');
        $this->iterator = new comp_type_iterator($input);
    }

    /* @return comp_type */
    public function next(){
        return parent::next();
    }
    
    protected  function set_statics() {
        parent::set_statics();
    }
}

class comp_type_iterator extends table_iterator {

    /* @return comp_type */
    public function key(){
        return parent::key();
    }
}