<?php

namespace module\tables\model;

use classes\table_array;
use html\node;
use model\flight;
use model\pilot;
use model\scorable;

class result_league extends result {

    function make_table(league_table $data): string {
        $array = new table_array();
        /* @var flight $t */
        foreach ($data->flights as $t) {
            /** @var pilot $class */
            if ($data->options->split_classes && $t->class == 5) {
                $t->ClassID += 8000;
            }
            if (isset ($array [$t->ClassID])) {
                $class = $array[$t->ClassID];
                $class->add_flight($t);
            } else {
                $class = new $data->class();
                $class->set_from_flight($t, $data->max_flights, $data->options->split_classes);
                $array[$t->ClassID] = $class;
            }
        }
        if (count($array) > 0) {
            $array->uasort(['\module\tables\model\league_table', 'cmp']);
            $class1 = 1;
            $class5 = 1;
            $html = node::create('div#table_wrapper.table_wrapper', [],
                node::create('h3.heading', [], $data->Title) .
                ($data->options->show_top_4 ? $data->ShowTop4($data->WHERE) : '') .
                node::create('table.results.main', [],
                    $data->write_table_header($data->max_flights, $data->class_primary_key) .
                    $array->iterate_return(
                        function (scorable $pilot) use (&$class1, &$class5) {
                            if ($pilot->class == 1) {
                                return $pilot->output($class1++);
                            } else {
                                return $pilot->output($class5++);
                            }
                        }
                    )
                )
            );
        } else {
            $html = node::create('table.main th.c', ['style' => 'width:663px'], 'No Flights to display');
        }
        return $html;
    }
}

