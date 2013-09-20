<?php
namespace flight_info;
class controller extends \core_module {

    /** @var \flight current */
    public $current;

    public function __controller(array $path) {
        if (isset($path[1])) {
            $this->view = 'flight';
            $this->current = new \flight();
            $this->current->do_retrieve(\flight::$default_fields, array('where_equals' => array('fid' => $path[1]), 'join' => \flight::$default_joins));
            if (!$this->current->fid) {
                \get::header_redirect('/flight_info');
            }
        }
        parent::__controller($path);
    }

}