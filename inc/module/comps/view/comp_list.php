<?php

namespace module\comps\view;

use module\comps\model\comp;
use template\html;

class comp_list extends html {

    function get_view(): string {
        $comps = comp::get_all(['type', 'round', 'task', 'comp.title AS title', 'date', 'cid', 'comp_group.title', 'file'], ['join' => ['comp_group' => 'comp.cgid = comp_group.cgid'], 'order' => 'date DESC, round DESC, task DESC, comp.cgid ASC']);
        return "
<div id='comp_wrapper'>
    <div id='comp_inner'>
        <div id='comp_list'>
            <h1 class='page-header'>Select a Competition</h1>
            <table class='main results'>
                <thead>
                <tr>
                    <th class='left'>Comp</th>
                    <th class='left'>Round</th>
                    <th class='left'>Task</th>
                    <th class='left'>Class</th>
                    <th class='left'>Title</th>
                    <th class='left'>Date</th>
                    <th class='left'></th>
                </tr>
                </thead>
                <tbody>
                {$comps->reduce(fn($_, comp $comp) => "$_
                    <tr>
                        <td class='left'>{$comp->type}</td>
                        <td class='left'>{$comp->round}</td>
                        <td class='left'>{$comp->task}</td>
                        <td class='left'>{$comp->comp_group->title}</td>
                        <td class='left'>{$comp->title}</td>
                        <td class='left'>{$comp->format_date($comp->date,'d/m/Y')}</td>
                        <td class='left'><a class='button' href='{$comp->get_url()}' data-click='{$comp->get_primary_key()}'>View</a></td>
                    </tr>")}
                </tbody>
            </table>
        </div>
        <div id='comp_view'></div>
    </div>
</div>


<script>
    var load_callback = load_callback || [];
    load_callback.push(function () {
        $('#comp').on('click', '#comp_list ul li a', function () {
            var id = $(this).attr('data-click');
            page('/comp/' + id);
        });
    });
</script>";
    }
}
