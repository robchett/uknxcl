<?php
namespace comps;
class _default_view extends \view {
    public function get_view() {
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
            /** @var comp $comp */
            $html .= '
            <tr>
                <td>' . $comp->type . '</td>
                <td>Round ' . (int) $comp->round . '</td>
                <td>Task ' . (int) $comp->task . '</td>
                <td>' . $comp->class . '</td>
                <td>' . $comp->title . '</td>
                <td>' . date('d/m/Y', strtotime($comp->date)) . '</td>
                <td><a class="button" href="' . $comp->get_url() . '" >View</a></td>
            </tr>';
        }
        //});
        $html .= "</table>";
        $html .= "</div>";


        $html .= '<div id="comp_view">';
        $html .= "</div>";

        $html .= "</div>";
        $html .= "</div>";

        $script =
            "$('#comp').on('click','#comp_list ul li a',function () {
                cpid = $(this).attr('data-click');
                page('/comp/' + cpid);
            });";

        if (ajax) {
            \ajax::add_script($script);
        } else {
            \core::$inline_script[] = $script;
        }

        return $html;
    }
}
