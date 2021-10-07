<?php

namespace track;

/**
 * @psalm-import-type JsonPart from igc_parser
 */
class track_part {

    public int $duration;
    public string $points;
    public int $skipped_distance;
    public int $skipped_duration;

    /** 
     * @param JsonPart $data
     */
    public function __construct(array $data) {
        $this->duration = $data['duration'];
        $this->points = $data['points'];
        $this->skipped_distance = $data['skipped_distance'] ?? 0;
        $this->skipped_duration = $data['skipped_duration'] ?? 0;
    }
}