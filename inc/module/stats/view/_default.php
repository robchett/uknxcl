<?php
namespace stats;
class _default_view extends \view {

    /** @return \html\node */
    public function get_view() {
        \core::$inline_script[] = '
        var $graph = new Graph($("#graph_contain"), 600);
        var obj = ' . $this->module->get_stats() . '
        obj.size = function() {
            return this.nxcl_data.track.length;
        }
        $graph.swap(obj);
        ';
        return $this->module->page_object->body . \html\node::create('div#graph_contain');
    }
}
