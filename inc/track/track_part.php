<?php
namespace track;

class track_part {

    public $end;
    public $end_point;
    public $start;
    public $start_point;
    public $time;

    public function __construct(track_point $point, $pos) {
        $this->start = $point;
        $this->start_point = $pos;
    }

    public function finish(track_point $point, $pos) {
        $this->end_point = $pos;
        $this->end = $point;
    }

    public function get_time() {
        if (!isset($this->time)) {
            $this->start->get_time_to($this->end);
        }
        return $this->time;
    }

    public function reduce_index($int) {
        $this->start_point -= $int;
        $this->end_point -= $int;
    }

    public function count() {
        return $this->end_point - $this->start_point + 1;
    }
}

 