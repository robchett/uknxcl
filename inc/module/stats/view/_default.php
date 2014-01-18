<?php
namespace module\stats\view;

use classes\view;
use html\node;

class _default extends \template\html {

    /** @var \module\stats\controller $module */
    public $module;

    /** @return node */
    public function get_view() {
        \core::$inline_script[] = '
        var $graph = new Graph($("#graph_contain"), 600);
        $graph.set_subsets([
            {"name": "Flights", "xAxis": "Flights", "min_value" : "min_flights", "max_value" : "max_flights", "index":2},
            {"name":"Score", "xAxis":"Points", "min_value" : "min_score", "max_value" : "max_score", "index":4}
        ]);
        var obj = ' . $this->module->get_stats() . '
        obj.size = function() {
            return this.nxcl_data.track.length;
        }
        $graph.init();
        $graph.swap(obj);
        ';
        return $this->module->page_object->body . node::create('div#graph_contain');
    }
}
