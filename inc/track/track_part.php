<?php
namespace track;

class track_part {

    public $duration;
    public $points;
    public $skipped_distance;
    public $skipped_duration;


    public function __construct() {
    }


    public function set_from_data($data) {
        $this->duration = $data->duration;
        $this->points = $data->points;
        $this->skipped_distance = $data->skipped_distance;
        $this->skipped_duration= $data->skipped_duration;
    }
}