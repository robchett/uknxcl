<?php

namespace module\tables\view;

use model\pilot;
use module\tables\model\league_table;
use template\html;

/** @extends html<\module\tables\controller, league_table> */
class skywings extends html {
    
    public function get_view(): string {
        $html = '';
        $league_table = new league_table();
        $league_table->get_flights();

        $array = [];
        foreach ($league_table->get_flights() as $t) {
            if (!isset($array [$league_table->getID($t)])) {
                $class = $league_table->getScorable($t);
                $class->output_function = 'csv';
                $class->set_from_flight($t, $league_table->max_flights, $league_table->split_classes);
                $array[$league_table->getID($t)] = $class;
            }
            $array[$league_table->getID($t)]->add_flight($t, $league_table->official);
        }
        usort($array, [league_table::class, 'cmp']);
        $class1 = 1;
        $class5 = 1;
        $html .= "<pre>Pos ,Name ,Glider ,Club ,Best ,Second ,Third ,Forth ,Fifth ,Sixth ,Total\n";
        for ($j = 0; $j < count($array); $j++) {
            if ($array [$j]->class == 1) {
                $html .= $array [$j]->output($league_table, $class1);
                $class1++;
            } else {
                $html .= $array [$j]->output($league_table, $class5);
                $class5++;
            }
        }
        $html .= "</pre>\n";
        return $html;
    }
}
