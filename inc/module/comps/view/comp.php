<?php

class comp_view extends view {

    /** @return html_node */
    public function get_view() {
        $html = '';
        $file = file_get_contents(root . '/uploads/comp/' . $this->module->current->cid . '/points.js');
        if ($file) {
            $data = json_decode($file);
            $html .= $data->html;
        }
        $html .= '<a class="comp_back button">Back To List</a>';

        core::$inline_script[] =
            "$('#comp_view a.comp_back').click(function () {
                $('#main').scrollTop(0);
            });
            map.callback = function() {
                map.add_comp(" . $this->module->current->cid . ");
            };
            ";
        return $html;
    }
}
 