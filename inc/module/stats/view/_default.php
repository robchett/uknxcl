<?php

namespace module\stats\view;

use classes\module;
use module\stats\controller;
use template\html;

class _default extends html {

    /** @var controller */
    public module $module;

    public function get_view(): string {
        $stats = $this->module->get_stats();
        $months = json_encode($stats['month']);
        $years = json_encode($stats['year']);
        $cols = count($stats['year']->nxcl_data->track[0]->data);
        return "
{$this->module->page_object->body}
<h1 class='page-header'>Flights flown by month</h1>
<div id='graph_contain'></div>
<h1 class='page-header'>Flights flown by year</h1>
<div id='graph_contain_2'></div>

<script>
    var load_callback = load_callback || [];
    load_callback.push(function () {
        var graph = new Graph($('#graph_contain'), 600);
        graph.set_subsets([
            {'name': 'Flights', 'xAxis': 'Flights', 'min_value': 'min_flights', 'max_value': 'max_flights', 'index': 1},
            {'name': 'Score', 'xAxis': 'Points', 'min_value': 'min_score', 'max_value': 'max_score', 'index': 2}
        ]);
        graph.legend.show = true;
        graph.grid.x.count = 12;
        graph.title = 'Month on month analysis';
        var obj = $months;
        obj.size = function () {
            return this.nxcl_data.track.length;
        };
        graph.type = 0;
        graph.swap(obj);

        var graph2 = new Graph($('#graph_contain_2'), 600);
        graph2.format = 'bar';
        graph2.type = 0;
        graph2.set_subsets([
            {'name': 'Flights', 'xAxis': 'Total', 'min_value': 'min', 'max_value': 'max', 'index': 1}
        ]);
        graph2.legend.show = true;
        graph2.grid.x.count = {$cols};
        graph2.title = 'Year on year analysis';
        obj = $years;
        obj.size = function () {
            return this.nxcl_data.track.length;
        };
        graph2.swap(obj);
    })
</script>";
    }
}
