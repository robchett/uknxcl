<?php

namespace track;


use classes\error_handler;
use Generator;
use JetBrains\PhpStorm\Pure;

class igc_parser {

    public $id;
    protected $data;

    public function exec($id, $data): bool {
        $file = root . '/.cache/' . $id . '/track.json';
        $this->id = $id;
        chdir(root . '/igc_parser');
        error_handler::debug('IGC Parser call', $data);
        $res = exec("igc_parser '" . json_encode($data) . "'");
        chdir(root);
        if (!$res) {
            return false;
        }
        file_put_contents($file, $res);
        $this->data = json_decode($res);
        return true;
    }

    public function load_data($id, $temp = true): bool {
        if ($temp) {
            $file = root . '/.cache/' . $id . '/track.json';
        } else {
            $file = root . '/uploads/flight/' . $id . '/track.json';
        }
        if (file_exists($file)) {
            $res = file_get_contents($file);
            $this->data = json_decode($res);
            return true;
        }
        return false;
    }

    public function get_duration() {
        return $this->data->duration;
    }

    public function get_validated() {
        return isset($this->data->validated) ? $this->data->validated : null;
    }

    public function has_height_data(): bool {
        return $this->data->stats->height->min != $this->data->stats->height->max;
    }

    public function is_winter(): bool {
        $month = $this->get_date('n');
        return (in_array($month, [1, 2, 12]));
    }

    public function get_date($format = null): bool|string {
        if (!$format) {
            return $this->data->date;
        } else {
            return date($format, strtotime($this->get_date()));
        }
    }

    public function get_start_time() {
        return $this->data->start_time;
    }

    #[Pure]
    public function get_part_count(): int {
        return count($this->data->sets);
    }

    public function is_task_completed(): bool {
        return isset($this->data->task->complete) && $this->data->task->complete;
    }

    public function get_task($type): ?task {
        if (isset($this->data->task->$type)) {
            $task = new task();
            $task->set_from_data($this->data->task->$type);
            return $task;
        }
        return null;
    }

    public function get_split_parts(): Generator {
        foreach ($this->data->sets as $index => $data) {
            $part = new track_part();
            $part->set_from_data($data);
            yield $index => $part;
        }
    }
}
