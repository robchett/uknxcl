<?php
namespace module\comps\view;

use classes\ajax;
use classes\view;
use html\node;
use module\add_flight\form\igc_form;
use module\comps\object\comp;
use object\flight;
use object\pilot;
use track\igc_parser;

class create extends \template\html {

    /** @vat \module\comps\controller */
    public $module;

    /** @return \html\node */
    public function get_view() {
        $root = root . '/uploads/comp/' . $this->module->current->get_primary_key();
        $files = glob($root . '/*.igc');
        $html = node::create('ul');

        foreach ($files as $file) {
            $name = str_replace($root, '', $file);
            $name = str_replace('.igc', '', $name);
            $name = preg_replace('/[0-9.\-_\/]/', ' ', $name);
            $name = preg_replace('/\s+/', ' ', $name);
            $name = trim($name);
            $pilot = new pilot();
            $parts = explode(' ', $name);
            $match = false;
            if ($pilot->do_retrieve([], ['where_equals'=>['name' => $name]]) || $pilot->do_retrieve([], ['where_equals'=>['name' => implode(' ', array_reverse($parts))]])) {
                $flight = new flight();
                $match = $flight->do_retrieve([], ['where_equals'=>['pid' => $pilot->get_primary_key(), 'date'=> date('Y-m-d', $this->module->current->date)]]);
            }
            $html->add_child(node::create('li a', ['data-ajax-post' => ['path' => $file, 'name' => $name, 'cid' => $this->module->current->get_primary_key()], 'data-ajax-click' => '\\module\\comps\\object\\comp:add_flight'], 'Process flight for ' . $name . ($match ? ' (Processed)' : '')));
        }
        \core::$inline_script[] = <<<'JS'
            $body.on("click","a.score_select",function () {
                var data = $(this).data("post");
                $("#temp_id").val(data.track);
                $("#type").val(data.type);
                $("#igc_upload_form").html("<p class='restart'>Your flight details have been saved, please complete the form below, 'Additional Details', to finalise your sumbission.<br/><a data-ajax-click='module\\add_flight\\form\\igc_upload_form:reset' href='#' class='button'>Restart</a></p>");
                $("#igc_form ").find("input.submit").removeAttr("disabled");
            });
JS;
        return node::create('form#igc_upload_form div#console.callout.callout-primary') . node::create('div#second_form.callout.callout-primary')  . $html;
    }
}
 