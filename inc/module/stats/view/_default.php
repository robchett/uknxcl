<?php
namespace module\stats\view;

use classes\view;
use html\node;

class _default extends \template\html {

    /** @var \module\stats\controller $module */
    public $module;

    /** @return node */
    public function get_view() {

        $stats = $this->module->get_stats();

        \core::$inline_script[] = '
        var $graph = new Graph($("#graph_contain"), 600);
        $graph.set_subsets([
            {"name": "Flights", "xAxis": "Flights", "min_value" : "min_flights", "max_value" : "max_flights", "index":1},
            {"name":"Score", "xAxis":"Points", "min_value" : "min_score", "max_value" : "max_score", "index":2}
        ]);
        $graph.legend.show = true;
        $graph.grid.x.count = 12;
        $graph.title = "Month on month analysis";
        var obj = ' . json_encode($stats['month']) . '
        obj.size = function() {
            return this.nxcl_data.track.length;
        }
        $graph.type = 0;
        $graph.init();
        $graph.swap(obj);

        var $graph2 = new Graph($("#graph_contain_2"), 600);
        $graph2.format = "bar"
        $graph.type = 0;
        $graph2.set_subsets([
            {"name": "Flights", "xAxis": "Total", "min_value" : "min", "max_value" : "max", "index":1}
        ]);
        $graph2.legend.show = true;
        $graph2.grid.x.count = ' . count($stats['year']->nxcl_data->track[0]->coords) . ';
        $graph2.title = "Year on year analysis";
        var obj = ' . json_encode($stats['year']) . '
        obj.size = function() {
            return this.nxcl_data.track.length;
        }
        $graph2.init();
        $graph2.swap(obj);

        ';
        return $this->module->page_object->body . node::create('div#graph_contain') . node::create('div#graph_contain_2');
    }
}
