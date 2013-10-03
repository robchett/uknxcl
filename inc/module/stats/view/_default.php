<?php
namespace module\stats\view;

use classes\view;
use html\node;

class _default extends view {

    /** @var \module\stats\controller $module */
    public $module;

    /** @return node */
    public function get_view() {
        \core::$inline_script[] = '
        var $graph = new Graph($("#graph_contain"), 600);
        var obj = ' . $this->module->get_stats() . '
        obj.size = function() {
            return this.nxcl_data.track.length;
        }
        $graph.swap(obj);
        ';
        return $this->module->page_object->body . node::create('div#graph_contain');
    }
}
