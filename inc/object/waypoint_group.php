<?php 
class waypoint_group { use table;
    public $table_key = 'wgid';
    public static $module_id = 21;
    
    /* @return waypoint_group_array */
    public static function get_all(array $fields, array $options = array()){
        return waypoint_group_array::get_all($fields, $options);
    }
}

class waypoint_group_array extends table_array {
    
    public function __construct($input = array()){
        parent::__construct($input,0,'waypoint_group_iterator');
        $this->iterator = new waypoint_group_iterator($input);
    }

    /* @return waypoint_group */
    public function next(){
        return parent::next();
    }
    
    protected  function set_statics() {
        parent::set_statics();
    }
}

class waypoint_group_iterator extends table_iterator {

    /* @return waypoint_group */
    public function key(){
        return parent::key();
    }
}