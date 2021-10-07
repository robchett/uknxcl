<?php

namespace track;

/**
 * @psalm-import-type JsonTask from igc_parser
 * @psalm-import-type JsonCoordinate from igc_parser
 */
class task {

    const TYPE_OPEN_DISTANCE = 1;
    const TYPE_OUT_AND_RETURN = 2;
    const TYPE_GOAL = 3;
    const TYPE_TRIANGLE = 4;
    const TYPE_FLAT_TRIANGLE = 5;

    static array $names = [self::TYPE_OPEN_DISTANCE => 'Open Distance', self::TYPE_OUT_AND_RETURN => 'Out & Return', self::TYPE_TRIANGLE => 'Triangle', self::TYPE_FLAT_TRIANGLE => 'Flat Triangle',];

    public int $duration;
    public float $distance;
    public int $type;
    /** @var JsonCoordinate[] */
    public array $coordinates;
    public string $title;
    public array $waypoints;

    /**
     * @param JsonTask $data
     */
    public function __construct(array $data) {
        $this->duration = $data['duration'];
        $this->distance = $data['distance'];
        /** @psalm-suppress MixedAssignment */
        $this->coordinates = $data['coordinates']; 
        $this->type = match($data['type']) {
            '0' => self::TYPE_OPEN_DISTANCE,
            '1' => self::TYPE_OUT_AND_RETURN,
            '2' => self::TYPE_GOAL,
            '3' => self::TYPE_TRIANGLE,
            '4' => self::TYPE_FLAT_TRIANGLE,
        };
        $this->title = (string) static::$names[$this->type];
    }

    public function load_from_data(array $data): void {

    }

    public function get_distance(): float {
        return $this->distance;
    }

    public function get_duration(): int {
        return $this->duration;
    }

    public function get_gridref(): string {
        $coords = [];
        foreach ($this->coordinates as $coordinate) {
            $coords[] = $coordinate['os_gridref'];
        }
        return implode(';', $coords);
    }

    public function set_from_data(array $data): void {

    }
}