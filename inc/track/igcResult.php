<?php

namespace track;

use classes\error_handler;
use Generator;

/**
 * @psalm-import-type JsonTrack from igc_parser
 * @psalm-import-type JsonStats from igc_parser
 */
class igcResult {

    public int $duration;
    /** @var JsonStats */
    public array $stats;
    /** @var array{open_distance: ?task, out_and_return: ?task, triangle: ?task, flat_triangle: ?task} */
    public array $task;
    public int $setCount;
    public ?bool $validated = null;
    public string $date;
    public int $start_time;
    public int $total_points;
    public int $points;
    public bool $completed = false;

    /** @param JsonTrack $data */
    public function __construct(public int $id, array $data) {
        $this->validated = isset($data['validated']) ? (bool) $data['validated'] : null;
        $this->total_points = $data['total_points'];
        $this->points = $data['points'];
        $this->start_time = $data['start_time'];
        $this->duration = $data['duration'];
        $this->date = $data['date'];
        $this->sets = [];
        $this->stats = $data['stats'];
        if (isset($data['task']['complete'])) {
            $this->completed = (bool) $data['task']['complete'];
        }
        unset($data['task']['complete']);
        $this->task = array_map(fn(?array $arr): ?task => $arr ? new task($arr) : null, $data['task']);
        return true;
    }

    public function get_duration(): int {
        return $this->duration;
    }

    public function has_height_data(): bool {
        return $this->stats['height']['min'] != $this->stats['height']['max'];
    }

    public function is_winter(): bool {
        $month = $this->get_date('n');
        return (in_array($month, [1, 2, 12]));
    }

    public function get_date(?string $format = null): string {
        if (!$format) {
            return $this->date;
        } else {
            return date($format, strtotime($this->date));
        }
    }

    public function get_start_time(): int {
        return $this->start_time;
    }

    public function get_part_count(): int {
        return count($this->sets);
    }

    public function is_task_completed(): bool {
        return $this->completed;
    }

    /** @param 'open_distance'|'out_and_return'|'triangle'|'flat_triangle'|'declared' $type */
    public function get_task(string $type): ?task {
        return $this->task[$type] ?? null;
    }
}
