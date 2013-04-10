<?php
function makeTable(league_table $data) {
    $html = '';
    $pilots = 2000;
    if ($data->official) {
        $pilots = 4;
    }
    $pilots_array = array();
    foreach ($data->flights as $flight) {
        if (isset ($pilots_array[$flight->ClassID . '.' . $flight->c_name])) {
            $pilots_array [$flight->ClassID . '.' . $flight->c_name]->add_flight($flight);
        } else {
            $pilot = new $data->class();
            $pilot->set_from_flight($flight, $data->max_flights, false);
            $pilots_array [$flight->ClassID . '.' . $flight->c_name] = $pilot;
        }
    }
    // Sort pilots by score.
    if (sizeof($pilots_array) > 0) {
        usort($pilots_array, "cmp");
    } else {
        $html .= " <table class='Main' ><th class=\"c\"style=\"width:663px\">No Flights to display</th></table>";
    }
    $Clubarray = array();
    for ($i = 0; $i < sizeof($pilots_array); $i++) {
        if (isset ($Clubarray [$pilots_array[$i]->name])) {
            $Clubarray [$pilots_array [$i]->name]->AddSub($pilots_array [$i], $data->max_flights);
        } else {
            $club = new $data->SClass();
            $club->set_from_pilot($pilots_array [$i], $pilots, $data->max_flights);
            $Clubarray [$pilots_array [$i]->name] = $club;
        }
    }
    if (isset ($Clubarray)) {
        usort($Clubarray, "cmp");
    }
    $html .= '<div class="table_wrapper"><h3>' . $data->Title . '</h3>';

    for ($j = 0; $j < sizeof($Clubarray); $j++) {
        $html .= $Clubarray [$j]->writeClubSemiHead($j + 1);
        $html .= $data->write_table_header($data->max_flights, $data->class_primary_key);
        $html .= $Clubarray[$j]->content;
        $html .= "</table>";
    }

    $html .= '</div>';

    return $html;
}

function FlightOrder($a, $b) {
    if ($a ['Score'] == $b ['Score']) {
        return 0;
    }
    return ($a ['Score'] > $b ['Score']) ? -1 : 1;
}

?>