<?php

namespace module\tables\model;

use html\node;
use model\flight;

class result_list extends result {

    function make_table(league_table $data): string {
        return "
        <div id='table_wrapper' class='table_wrapper'>
            <h3 class='heading'>$data->Title</h3>
            <table class='Pilot main results'>
                <thead>
                    <tr>
                        <th style='width:45px'>Flight No</th>
                        <th style='width:60px'>Name</th>
                        <th style='width:60px'>Date</th>
                        <th style='width:90px'>Club</th>
                        <th style='width:95px'>Glider</th>
                        <th style='width:60px'>Score</th>
                        <th style'=width:298px'>Flight Waypoints</th>
                    </tr>
                </thead>
                " . $data->get_flights()->iterate_return(fn (flight $flight): string =>
                    "<tr>
                        <td>{$flight->fid}</td>
                        <td>{$data->getTitle($flight)}</td>
                        <td>{$flight->format_date($flight->date, 'd/m/y')}</td>
                        <td>{$data->getSubTitle($flight)}</td>
                        <td>{$flight->glider->name}</td>
                        {$flight->to_print()}
                        <td>{$flight->coords}</td>
                    </tr>"
                ) . "
            </table>
        </div>";
    }
}