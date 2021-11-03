<?php

namespace module\tables\model;

use classes\table_array;
use html\node;
use model\club;
use model\manufacturer;
use model\flight;
use model\pilot;
use model\scorable;

class result_club extends result
{

    function make_table(league_table $data): string
    {
        $pilots = $data->official ? 4 : 2000;
        /** @var table_array<scorable> */
        $pilots_array = new table_array();
        foreach ($data->get_flights() as $flight) {
            if (!isset($pilots_array[$data->getID($flight) . '.' . $data->getSubTitle($flight)])) {
                /** @var pilot $pilot */
                $pilot = $data->getScorable($flight);
                $pilot->set_from_flight($flight, $data->max_flights, false);
                $pilots_array[$data->getID($flight) . '.' . $data->getSubTitle($flight)] = $pilot;
            }
            $pilots_array[$data->getID($flight) . '.' . $data->getSubTitle($flight)]->add_flight($flight, $data->official);
        }
        if (!$pilots_array->count()) {
            return "<table class='main'><thead><tr><th class='c' style='width:663px'>No Flights to display</th></tr></thead></table>";
        }
        // Sort pilots by score.
        $pilots_array->uasort([league_table::class, 'cmp']);
        /** @var table_array<club|manufacturer> */
        $club_array = new table_array();
        $pilots_array->iterate(function (scorable $pilot) use ($data, $pilots, $club_array) {
            /** @var club $club */
            $subtitle = $data->getSubTitle($pilot->flightObjects[0]);
            if (!isset($club_array[$subtitle])) {
                $club = $data->getSubScorable($pilot->flightObjects[0]);                 
                $club->set_from_pilot($data, $pilot, $pilots);
                $club_array[$subtitle] = $club;
            } else {
                $club_array[$subtitle]->AddSub($data, $pilot);
            }
        });
        $club_array->uasort([league_table::class, 'cmp']);

        return "
        <div id='table_wrapper' class='table_wrapper'>
            <h3 class='heading'>$data->Title</h3>
                " . $club_array->iterate_return(function (club|manufacturer $club, $i) use ($data): string {
                    return "
                    <div id='table_wrapper' class='table_wrapper inner'>
                        " . $club->writeClubSemiHead($i + 1) . "
                        <table class='results main flights_' . $data->max_flights'>
                            " . $data->write_table_header($data->max_flights, $data->class_table_alias) . "
                            $club->content
                        </table>
                    </div>";
                }) . "
        </div>";
    }
}