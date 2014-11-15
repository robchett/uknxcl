<?php
namespace track;

class defined_task extends \task {

    public function __construct($coordinates) {
        $points = explode(';', $coordinates);
        $points = [];
        foreach ($points as &$a) {
            $points[] = \classes\geometry::os_to_lat_long($a);
        }
        if(count($points) == 5) {
            parent::__construct($points[0], $points[1], $points[2], $points[3], $points[4]);
        } else if(count($points) == 4) {
            parent::__construct($points[0], $points[1], $points[2], $points[3]);
        } else if(count($points) == 3) {
            parent::__construct($points[0], $points[1], $points[2]);
        } else if(count($points) == 2) {
            parent::__construct($points[0], $points[1]);
        }
    }

    public function is_valid(track $set) {
        return $this->completes_task();
    }
}