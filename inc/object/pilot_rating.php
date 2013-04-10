<?php 
class pilot_rating extends table {
    public $table_key = 'prid';
    public static $module_id = 15;
    
    /* @return pilot_rating_array */
    public static function get_all(array $fields, array $options = array()){
        return pilot_rating_array::get_all($fields, $options);
    }
}

class pilot_rating_array extends table_array {
    
    public function __construct($input = array()){
        parent::__construct($input,0,'pilot_rating_iterator');
        $this->iterator = new pilot_rating_iterator($input);
    }

    /* @return pilot_rating */
    public function next(){
        return parent::next();
    }
    
    protected  function set_statics() {
        parent::set_statics();
    }
}

class pilot_rating_iterator extends table_iterator {

    /* @return pilot_rating */
    public function key(){
        return parent::key();
    }
}