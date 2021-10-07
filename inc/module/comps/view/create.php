<?php

namespace module\comps\view;

use classes\attribute_callable;
use classes\module;
use model\flight;
use model\pilot;
use template\html;

/** @extends html<\module\comps\controller, \module\comps\model\comp> */
class create extends html {

    public function get_view(): string {
        $root = root . '/uploads/comp/' . $this->current->get_primary_key();
        $files = glob($root . '/*.igc');

        $flights = '';

        foreach ($files as $file) {
            $name = str_replace($root, '', $file);
            $name = str_replace('.igc', '', $name);
            $name = preg_replace('/[0-9.\-_\/]/', ' ', $name);
            $name = preg_replace('/\s+/', ' ', $name);
            $name = trim($name);
            $parts = explode(' ', $name);
            $pilot = pilot::get(new \classes\tableOptions(where_equals: ['name' => $name])) ?: pilot::get(new \classes\tableOptions(where_equals: ['name' => implode(' ', array_reverse($parts))]));
            $match = '';
            if ($pilot) {
                $match = flight::get(new \classes\tableOptions(where_equals: ['pid' => $pilot->get_primary_key(), 'date' => date('Y-m-d', $this->current->date)])) ? ' (Processed)' : '';
            }
            $data = json_encode(['path' => $file, 'name' => $name, 'cid' => $this->current->get_primary_key()]);
            $callable = attribute_callable::create([\module\comps\model\comp::class, 'add_flight']);
            $flights .= "
<li>
    <a class='btn' data-ajaxpost='{$data}'  data-ajaxclick='$callable'>Process flight for {$name}$match</a>
</li>";
        }
        return "
<form id='igc_upload_form'>
    <div id='console' class='callout callout-primary'></div>
</form>
<div id='second_form' class='callout callout-primary'></div>

<ul>$flights</ul>
<script>
    var load_callback = load_callback || [];
    load_callback.push(function () {
        \$body.on('click', 'a.score_select', function () {
            var data = $(this).data('post');
            $('#temp_id').val(data.track);
            $('#type').val(data.type);
            $('#igc_upload_form').html('<div id='console' class='callout callout-primary'></div>')
            $('#igc_form ').find('input.submit').removeAttr('disabled');
        });
})
</script>
        ";
    }
}
 