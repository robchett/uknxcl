<?php

namespace module\tables\model;

use classes\table_array;
use html\node;
use model\flight;
use model\pilot;
use model\scorable;

class result_league extends result {

    function make_table(league_table $data): string {
        /** @var table_array<scorable> */
        $array = new table_array();
        foreach ($data->get_flights() as $flight) {
            /** @var pilot $class */
            if (!isset ($array[$data->getID($flight)])) {
                $class = $data->getScorable($flight);
                $class->set_from_flight($flight, $data->max_flights, $data->split_classes);
                $array[$data->getID($flight)] = $class;
            }
            $array[$data->getID($flight)]->add_flight($flight, $data->official);
        }
        if (count($array) == 0) {
            return '<table class="main"><th class="c" style="width:663px">No Flights to display</th></table>';
        }
        $array->uasort([league_table::class, 'cmp']);
        $class1 = 1;
        $class5 = 1;
        $top4 = ($data->show_top_4 ? $data->ShowTop4() : '');
        return "
        <div id='table_wrapper' class='table_wrapper'>
            <h3 class='heading'>$data->Title</h3>
            $top4
            <table class='results main'>
                " . $data->write_table_header($data->max_flights, $data->class_table_alias) . "
                " . $array->iterate_return(
                    /** @psalm-suppress all */
                    function (scorable $pilot) use ($data, &$class1, &$class5): string { return $pilot->output($data, $pilot->class == 1 ? $class1++ : $class5++); }
                ) . "
            </table>
        </div>";
    }
}

