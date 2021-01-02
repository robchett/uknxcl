<?php

namespace model;

class pilot_official extends pilot {

    public bool $defined = false;
    public bool $undefined = false;
    public int $used_flights = 0;

    public function add_flight(flight $flight) {
        if ($this->used_flights < $this->max_flights - 3) {
            $this->_add_flight($flight);
        } else if ($this->used_flights == $this->max_flights - 3) {
            if (($this->defined || $this->undefined) || ($flight->defined && !$this->defined) || ($flight->ftid == 1 && !$this->undefined)) {
                $this->_add_flight($flight);
            }
        } else if ($this->used_flights == $this->max_flights - 2) {
            if (($this->defined && $this->undefined) || ($flight->defined && !$this->defined) || ($flight->ftid == 1 && !$this->undefined)) {
                $this->score += $flight->score;
                $this->flights[] = (string)$flight->to_print();
                $this->used_flights++;
            }
        }
        $this->total += $flight->score;
        $this->number_of_flights++;
    }

    private function _add_flight(flight $flight) {
        $this->score += $flight->score;
        $this->flights[] = (string)$flight->to_print();
        $this->used_flights++;
        if ($flight->defined) {
            $this->defined = true;
        }
        if ($flight->ftid == 1) {
            $this->undefined = true;
        }
    }

    function set_from_flight(flight $flight, $num = 6, $split = false) {
        $this->max_flights = $num;
        $this->name = $flight->p_name;
        $this->club = $flight->c_name;
        $this->glider = $flight->g_name;
        $this->score += $flight->score;
        $this->total += $flight->score;
        $this->number_of_flights = 1;
        $this->flights[] = (string)$flight->to_print();
        if ($flight->defined) {
            $this->defined = true;
        } else if ($flight->ftid == 1) {
            $this->undefined = true;
        }
        if ($split) {
            $this->class = $flight->class;
        } else {
            $this->class = 1;
        }
        $this->id = $flight->ClassID;
        $this->name = $flight->p_name;
    }
}