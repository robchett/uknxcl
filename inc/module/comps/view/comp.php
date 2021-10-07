<?php

namespace module\comps\view;

use classes\module;
use template\html;

/** 
 * @extends html<\module\comps\controller, \module\comps\model\comp>
 * 
 */
class comp extends html {

    function get_view(): string {
        $file = file_get_contents($this->current->get_js_file());
        $flights = '';
        $turnpoints = '';
        /** @var array{turnpoints: int, flights: array{pilot: string}[]} $data */
        $data = json_decode($file, true);
        if ($data) {
            for ($i = 0; $i < $data['turnpoints']; $i++) {
                $turnpoints .= "<th>Turnpoint {$i}</th>";
            }
            foreach ($data['flights'] as $f) {
                $flights .= "<tr><td>{$f['pilot']}</td>";
                for ($i = 0; $i < $data['turnpoints']; $i++) {
                    $hit = $f[$i] > 0 ? '&#10004;' : '&#10006;';
                    $flights .= "<td>{$hit}</td>";
                }
                $flights .= "<td><input type='checkbox' checked='checked'></td></tr>";
            }
        }

        return "
<table class='main results'>
    <thead>
    <tr>
        <th>Pilot</th>
        $turnpoints
        <th>On map</th>
    </tr>
    </thead>
    $flights 
</table>

<a class='comp_back btn' href='/comps'>Back to list</a>

<script>
    var load_callback = load_callback || [];
    load_callback.push(function () {
        map.callback(function (map) {
            map.add_comp({$this->current->get_primary_key()})
        });
    });
</script>";
    }
}
 