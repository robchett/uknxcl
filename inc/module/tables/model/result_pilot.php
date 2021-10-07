<?php

namespace module\tables\model;

use classes\ajax;
use core;
use html\node;
use model\flight;

class result_pilot extends result {

    function make_table(league_table $data): string {
        $html = "
        <div id='table_wrapper' class='table_wrapper>
            <h3 class='heading'>$data->Title</h3>
            <table class='main results tablesorter'>
                <thead>
                    <tr>                        
                        <th style='width:42px'>Flight No</th>
                        <th style='width:63px'>Date</th>
                        <th style='width:90px'>Club</th>
                        <th style='width:95px'>Glider</th>
                        <th style='width:60px'>Score</th>
                        <th style='width:298px'>Flight Waypoints</th>
                    </tr>
                </thead>                    
                " . $data->get_flights()->iterate_return(fn(flight $flight): string => "
                <tr>
                    <td>{$flight->fid}</td>
                    <td>" . date('d/m/Y', $flight->date) . "</td>
                    <td>{$data->getSubTitle($flight)}</td><td>{$flight->glider->name}</td>
                    {$flight->to_print()}
                    <td>{$flight->coords}</td>
                </tr>") . "
            </table>
        </div>";
        $script = '$("table.main").tablesorter( {
        headers: {
            0: {sorter: false},
            1: {sorter: "uk_date"},
            2: {sorter: false},
            3: {sorter: false},
            4: {sorter: "score"},
            5: {sorter: false},
        }
    } ); ';
        if (ajax) {
            ajax::add_script($script);
        } else {
            core::$inline_script[] = $script;
        }
        return $html;
    }
}