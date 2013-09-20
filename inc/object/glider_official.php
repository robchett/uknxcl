<?php
class glider_official extends pilot_official {
    /** @var string */
    public $g_name;

    public function set_from_flight(flight $flight, $num = 6, $split = false) {
        parent::set_from_flight($flight, $num, $split);
        if ($this->number_of_flights == 1) {
            $this->club = $flight->gm_title;
            $this->name = $flight->g_name;
        }
    }

    public function output_table($pos) {
        $flights = implode('', $this->flights);
        for ($i = count($this->flights); $i < $this->max_flights; $i++) {
            $flights .= '<td></td>';
        }
        return '
<tr class="class' . $this->class . '">
    <td>' . $pos . '</td>
    <td>' . $this->glider . '</td>
    <td>' . $this->club . '</td>
    ' . $flights . '
    <td>' . $this->score . ($this->score == $this->total ? '' : '<br/>' . $this->total) . ' (' . $this->number_of_flights . ')</td>
</tr>' . "\n";
    }
}

