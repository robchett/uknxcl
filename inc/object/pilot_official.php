<?php

class pilot_official extends pilot {
    public $defined = false;
    public $undefined = false;
    public $used_flights = 0;

    public function add_flight(flight $flight) {
        if ($this->used_flights < $this->max_flights - 2) {
            $this->score += $flight->score;
            $this->flights[] = $flight->to_print()->get();
            $this->used_flights++;
            if ($flight->defined) {
                $this->defined = true;
            }
            if ($flight->ftid == 1) {
                $this->undefined = true;
            }
        } else if ($this->used_flights == $this->max_flights - 2) {
            if (($this->defined && $this->undefined) || ($flight->defined && !$this->defined) || ($flight->ftid == 1 && !$this->undefined)) {
                $this->score += $flight->score;
                $this->flights[] = $flight->to_print()->get();
                $this->used_flights++;
            }
        }
        $this->total += $flight->score;
        $this->number_of_flights++;
    }

    function set_from_flight(flight $flight, $num = 6, $split = false) {
        $this->max_flights = $num;
        $this->name = $flight->p_name;
        $this->club = $flight->c_name;
        $this->glider = $flight->g_name;
        $this->score += $flight->score;
        $this->total += $flight->score;
        $this->number_of_flights = 1;
        $this->flights[] = $flight->to_print()->get();
        if ($flight->defined)
            $this->defined = true;
        else
            $this->undefined = true;
        if ($split == 1)
            $this->class = $flight->class;
        else
            $this->class = 1;
        $this->id = $flight->ClassID;
        $this->name = $flight->p_name;
    }
}