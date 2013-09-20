<?php
namespace comps;
class comp_view extends \view {

    /** @return \html\node */
    public function get_view() {
        $html = '';
        $file = file_get_contents(root . '/uploads/comp/' . $this->module->current->cid . '/points.js');
        if ($file) {
            $data = json_decode($file);
            $html .= $data->html;
        }
        $html .= '<a href="/comps" class="comp_back button">Back To List</a>';

        if (!ajax) {
            \core::$inline_script[] = "
            map.callback = function() {
                map.add_comp(" . $this->module->current->cid . ");
            }; ";
        } else {
            \ajax::add_script('map.add_comp(' . $this->module->current->cid . ');');
        }
        return $html;
    }
}
 