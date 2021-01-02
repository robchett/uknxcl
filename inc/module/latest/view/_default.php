<?php

namespace module\latest\view;

use model\flight;
use template\html;

class _default extends html {

    function get_view(): string {
        $flights = flight::get_all(['flight.*', 'pilot.name', 'pilot.pid'], ['join' => ['pilot' => 'flight.pid = pilot.pid'], 'where' => '`delayed` = 0 AND personal = 0', 'limit' => 40, 'order' => 'fid DESC']);
        return "
<h1 class='page-header'>Latest</h1>
<div class='table_wrapper'>
    <table class='results main'>
        <thead>
        <tr>
            <th class='left'>ID</th>
            <th class='left'>Pilot</th>
            <th class='left'>Date Flown</th>
            <th class='left'>Date Added</th>
            <th class='left'>Score</th>
        </tr>
        </thead>
        <tbody>
        {$flights->reduce(fn($_, flight $flight) => "$_
            <tr>
                <td class='left'>{$flight->fid}</td>
                <td class='left'>{$flight->pilot->name}</td>
                <td class='left'>{$flight->format_date($flight->date, 'd/m/y')}</td>
                <td class='left'>{$flight->format_date($flight->created, 'd/m/y')}</td>
                {$flight->to_print()}
            </tr>")}
        </tbody>
    </table>
</div>";
    }
}
