<?php

class comps extends core_module {

    public $page = 'comp';

    public function do_generate_all() {
        $comps = comp::get_all(array());
        /** @var $comp comp */
        foreach ($comps as $comp) {
            $comp->do_zip_to_comp();
        }
        //});
    }

    public function get() {
        $comps = comp_array::get_all(array('type', 'round', 'task', 'comp.title AS title', 'date', 'cid', 'comp_group.title AS class', 'file'), array('join' => array('comp_group' => 'comp.class = comp_group.cgid'), 'order' => 'date DESC, round DESC, task DESC, class ASC'));
        $html = '';
        $html .= '<div id="comp_wrapper">';
        $html .= '<div id="comp_inner">';
        $html .= '<div id="comp_list">';

        $html .= '<h2>Select a Competition</h2>';
        $html .= '<table>';
        $html .= '
        <thead>
            <th>Comp</th>
            <th>Round</th>
            <th>Task</th>
            <th>Class</th>
            <th>Title</th>
            <th>Date</th>
            <th></th>
        </thead>';
        //$comps->iterate(function ($comp) use (&$html) {
        foreach ($comps as $comp) {
            $html .= '
            <tr>
                <td>' . $comp->type . '</td>
                <td>Round ' . (int) $comp->round . '</td>
                <td>Task ' . (int) $comp->task . '</td>
                <td>' . $comp->class . '</td>
                <td>' . $comp->title . '</td>
                <td>' . date('d/m/Y',strtotime($comp->date)) . '</td>
                <td><a class="button" onclick="map.add_comp(' . $comp->cid . ')">View</a></td>
            </tr>';
        }
        //});
        $html .= "</table>";
        $html .= "</div>";


        $html .= '<div id="comp_view">';
        $html .= '<div id="WriteHereComp"></div>';
        $html .= '<a class="comp_back button">Back To List</a>';
        $html .= "</div>";

        $html .= "</div>";
        $html .= "</div>";

        $script =
            "$('#comp').on('click','#comp_list ul li a',function () {
                cpid = $(this).attr('data-click');
                map.add_comp(cpid);
            });
            $('#comp_view a.comp_back').click(function () {
                $('#comp_inner').animate({'left': 0});
            });";

        if (ajax) {
            ajax::add_script($script);
        } else {
            core::$inline_script[] = $script;
        }

        return $html;
    }

}
