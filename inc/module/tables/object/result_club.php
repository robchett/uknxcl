<?php
namespace module\tables\object;

use classes\table_array;
use html\node;
use object\club;
use object\pilot;
use object\scorable;

class result_club extends result {

    function make_table(league_table $data) {
        $pilots = 2000;
        if ($data->options->official) {
            $pilots = 4;
        }
        $pilots_array = new table_array();
        /** @var \object\flight $flight */
        foreach ($data->flights as $flight) {
            if (isset ($pilots_array[$flight->ClassID . '.' . $flight->c_name])) {
                $pilots_array[$flight->ClassID . '.' . $flight->c_name]->add_flight($flight);
            } else {
                /** @var pilot $pilot */
                $pilot = new $data->class();
                $pilot->set_from_flight($flight, $data->max_flights, false);
                $pilots_array [$flight->ClassID . '.' . $flight->c_name] = $pilot;
            }
        }
        // Sort pilots by score.
        if ($pilots_array->count()) {
            $pilots_array->uasort(['\module\tables\object\league_table', 'cmp']);
        } else {
            return node::create('table.main thead tr th.c', ['style' => 'width:663px'], 'No Flights to display');
        }
        $club_array = new table_array();
        $pilots_array->iterate(function (scorable $pilot) use ($data, $pilots, $club_array) {
            /** @var club $club */
            if ($pilot->club) {
                if (isset ($club_array [$pilot->club])) {
                    $club = $club_array [$pilot->club];
                    $club->AddSub($pilot, $data->max_flights);
                } else {
                    $club = new $data->SClass();
                    $club->set_from_pilot($pilot, $pilots, $data->max_flights);
                    $club_array [$pilot->club] = $club;
                }
            }
        });
        $club_array->uasort(['\module\tables\object\league_table', 'cmp']);

        $html = node::create('div.table_wrapper', [],
            node::create('h3.heading', [], $data->Title) .
            $club_array->iterate_return(function (club $club, $i) use ($data) {
                return node::create('div.table_wrapper.inner', [],
                    $club->writeClubSemiHead($i + 1) .
                    node::create('table.results.main.flights_' . $data->max_flights, [],
                        $data->write_table_header($data->max_flights, $data->class_primary_key) .
                        $club->content
                    )
                );
            })
        );

        return $html;
    }

    function FlightOrder($a, $b) {
        if ($a ['Score'] == $b ['Score']) {
            return 0;
        }
        return ($a ['Score'] > $b ['Score']) ? -1 : 1;
    }
}

?>