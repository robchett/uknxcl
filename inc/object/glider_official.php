<?php

namespace object;

class glider_official extends pilot_official {

    /** @var string */
    public $g_name;
    public $primary_name = 'glider';
    public $secondary_name = 'club';
    public $tertiary_name = false;

    public function set_from_flight(flight $flight, $num = 6, $split = false) {
        parent::set_from_flight($flight, $num, $split);
        if ($this->number_of_flights == 1) {
            $this->club = $flight->gm_title;
            $this->name = $flight->g_name;
        }
    }
}

