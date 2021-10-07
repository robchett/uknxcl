<?php

namespace model;

use classes\ajax;
use classes\table;
use classes\interfaces\model_interface;
use form\form;
use module\tables\model\league_table;

class scorable implements model_interface {
    use table;

    public int $class = 1;
    /** @var string[] */
    public array $flights = [];
    /** @var flight[] */
    public array $flightObjects = [];
    /** @var 'table'|'csv' */
    public string $output_function = 'table';
    public int $max_flights = 6;
    public int $number_of_flights = 0;
    public float $score = 0;
    public float $total = 0;
    public bool $defined = false;
    public bool $undefined = false;
    public int $used_flights = 0;

    public function add_flight(flight $flight, bool $official): void {
        $this->total += $flight->score;
        $this->number_of_flights++;

        if ($this->max_flights == $this->used_flights) {
            return;
        }
        if (!$official) {
            $this->_add_flight($flight);
            return;
        }
        // First 4 flights can be anything
        if ($this->used_flights <= $this->max_flights - 2) {
            $this->_add_flight($flight);
            return;
        } 
        // Now look for a goal and open distance flight.
        // If we already have a defined or open distance flight then we can safely accept this one. Otherwise see if it matches either requirement
        if ($this->used_flights == $this->max_flights - 2 && ($this->defined || $this->undefined || $flight->defined || $flight->ftid == 1)) {
            $this->_add_flight($flight);
            return;
        }
        // Only accept the last flight if we've had both requirements or it matches the last one.
        if ($this->used_flights == $this->max_flights - 1 && (($this->defined || $this->undefined) && ($flight->defined && $this->undefined) || ($flight->ftid == 1 && $this->defined))) {
            $this->_add_flight($flight);
            return;
        }
    }

    private function _add_flight(flight $flight): void {
        $this->score += $flight->score;
        $this->flights[] = $flight->to_print();
        $this->flightObjects[] = $flight;
        $this->used_flights++;
        if ($flight->defined) {
            $this->defined = true;
        }
        if ($flight->ftid == 1) {
            $this->undefined = true;
        }
    }

    function set_from_flight(flight $flight, int $num = 6, bool $split = false): void {
        $this->max_flights = $num;
        $this->class = $split ? $flight->glider->class : 1;
    }

    public function output(league_table $leauge_table, int $pos): string {
        return match($this->output_function) {
            'table' => $this->output_table($leauge_table, $pos),
            'csv' => $this->output_csv($leauge_table, $pos),
        };
    }

    public function output_csv(league_table $leauge_table, int $pos): string {
        $csv = $pos . ',\'' . $leauge_table->getTitle($this->flightObjects[0]) . '\',\'' . $leauge_table->getSubTitle($this->flightObjects[0]) . '/' . $leauge_table->getTertiaryTitle($this->flightObjects[0]) . '\',' . strip_tags(implode(',', $this->flights));
        for ($i = $this->number_of_flights; $i < $this->max_flights - 1; $i++) {
            $csv .= ',';
        }
        $csv .= $this->score . ',' . $this->total . '(' . $this->number_of_flights . ')<br/>';
        return $csv;
    }

    public function output_table(league_table $leauge_table, int $pos): string {
        $flights = implode('', $this->flights);
        for ($i = count($this->flights); $i < $this->max_flights; $i++) {
            $flights .= '<td class="left"></td>';
        }
        return '
<tr class="class' . $this->class . '">
    <td class="left">' . $pos . '</td>
    <td class="left">' . $leauge_table->getTitle($this->flightObjects[0]) . '</td>
    <td class="left">' . $leauge_table->getSubTitle($this->flightObjects[0]) . '<br/>' . $leauge_table->getTertiaryTitle($this->flightObjects[0]) . '</td>
    ' . $flights . '
    <td class="left">' . $this->score . ($this->score == $this->total ? '' : '<br/>' . $this->total) . ' (' . $this->number_of_flights . ')</td>
</tr>';
    }
}

