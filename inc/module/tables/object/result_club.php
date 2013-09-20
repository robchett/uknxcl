<?php
namespace tables;
class result_club extends result {

    function make_table(league_table $data) {
        $html = '';
        $pilots = 2000;
        if ($data->options->official) {
            $pilots = 4;
        }
        /** @var \pilot[] $pilots_array */
        $pilots_array = array();
        /** @var \flight $flight */
        foreach ($data->flights as $flight) {
            if (isset ($pilots_array[$flight->ClassID . '.' . $flight->c_name])) {
                $pilots_array[$flight->ClassID . '.' . $flight->c_name]->add_flight($flight);
            } else {
                /** @var \pilot $pilot */
                $pilot = new $data->class();
                $pilot->set_from_flight($flight, $data->max_flights, false);
                $pilots_array [$flight->ClassID . '.' . $flight->c_name] = $pilot;
            }
        }
        // Sort pilots by score.
        if (count($pilots_array) > 0) {
            usort($pilots_array, ['tables\league_table', 'cmp']);
        } else {
            $html .= " <table class='Main' ><th class=\"c\" style=\"width:663px\">No Flights to display</th></table>";
        }
        /** @var \club[] $Clubarray */
        $Clubarray = array();
        for ($i = 0; $i < count($pilots_array); $i++) {
            if (isset ($Clubarray [$pilots_array[$i]->club])) {
                $Clubarray [$pilots_array [$i]->club]->AddSub($pilots_array [$i], $data->max_flights);
            } else {
                /** @var \club $club */
                $club = new $data->SClass();
                $club->set_from_pilot($pilots_array [$i], $pilots, $data->max_flights);
                $Clubarray [$pilots_array [$i]->club] = $club;
            }
        }
        if (isset ($Clubarray)) {
            usort($Clubarray, ['tables\league_table', 'cmp']);
        }
        $html .= '<div class="table_wrapper"><h3>' . $data->Title . '</h3>';

        for ($j = 0; $j < count($Clubarray); $j++) {
            $html .= $Clubarray [$j]->writeClubSemiHead($j + 1);
            $html .= $data->write_table_header($data->max_flights, $data->class_primary_key);
            $html .= $Clubarray[$j]->content;
            $html .= "</table>";
            $html .= "</div>";
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
}

?>