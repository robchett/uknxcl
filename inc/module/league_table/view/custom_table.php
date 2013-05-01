<?php
function makeTable(league_table $data) {
    $html = '';
    /* @var $array pilot[] */
    $array = array();
    /* @var $t flight */
    foreach ($data->flights as $t) {
        if ($data->split_classes && $t->class == 5) {
            $t->ClassID += 8000;
        }
        if (isset ($array [$t->ClassID])) {
            $array [$t->ClassID]->add_flight($t);
        } else {
            /** @var $class pilot */
            $class = new $data->class();
            $class->set_from_flight($t, $data->max_flights, $data->split_classes);
            $array[$t->ClassID] = $class;
        }
    }
    if (count($array) > 0) {
        usort($array, "cmp");
        $html .= '<div class="table_wrapper"><h3>' . $data->Title . '</h3>';
        // Print the top 4 flights of each type if wanted
        if ($data->show_top_4)
            $html .= $data->ShowTop4($data->WHERE);

        // Print the table header with the right number of flights
        $html .= $data->write_table_header($data->max_flights, $data->class_primary_key);

        $class1 = 1;
        $class5 = 1;
        foreach ($array as $pilot) {
            if ($pilot->class == 1) {
                $html .= $pilot->output($class1, 0);
                $class1++;
            } else {
                $html .= $pilot->output($class5, 0);
                $class5++;
            }
        }
        $html .= '</table></div>';
    } else
        $html .= "<table class='Main'><th class=\"c\" style=\"width:663px\">No Flights to display</th></table>";

    return $html;
}

?>