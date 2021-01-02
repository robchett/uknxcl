<?php

namespace model;

class glider_official extends pilot_official {

    /** @var string */
    public string $g_name;
    public string $primary_name = 'glider';
    public string $secondary_name = 'club';
    public ?string $tertiary_name = null;

    public function set_from_flight(flight $flight, $num = 6, $split = false) {
        parent::set_from_flight($flight, $num, $split);
        if ($this->number_of_flights == 1) {
            $this->club = $flight->gm_title;
            $this->name = $flight->g_name;
        }
    }
}

