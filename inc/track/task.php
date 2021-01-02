<?php

namespace track;

class task {

    const TYPE_OPEN_DISTANCE = 1;
    const TYPE_OUT_AND_RETURN = 2;
    const TYPE_GOAL = 3;
    const TYPE_TRIANGLE = 4;
    const TYPE_FLAT_TRIANGLE = 5;

    static array $names = [self::TYPE_OPEN_DISTANCE => 'Open Distance', self::TYPE_OUT_AND_RETURN => 'Out & Return', self::TYPE_TRIANGLE => 'Triangle', self::TYPE_FLAT_TRIANGLE => 'Flat Triangle',];

    public $duration;
    public $distance;
    public $type;
    public $coordinates;
    public string $title;
    public $waypoints;


    public function __construct() {
    }

    public function load_from_data($data) {

    }

    public function get_distance() {
        return $this->distance;
    }

    public function get_duration() {
        return $this->duration;
    }

    public function get_gridref(): string {
        $coords = [];
        foreach ($this->coordinates as $coordinate) {
            $coords[] = $coordinate->os_gridref;
        }
        return implode(';', $coords);
    }

    public function set_from_data($data) {
        $this->duration = $data->duration;
        $this->distance = $data->distance;
        $this->coordinates = $data->coordinates;
        $this->type = $data->type + 1;
        $this->title = static::$names[$this->type];
    }
}