<?php

namespace track;

use classes\error_handler;
use Generator;

/**
 * @psalm-type JsonStat=array{min: int, max: int}
 * @psalm-type JsonStats=array{height: JsonStat, speed: JsonStat, climb_rate: JsonStat}
 * @psalm-type JsonPart=array{duration: int, points: string, skipped_distance?: int, skipped_duration?: int}
 * @psalm-type JsonCoordinate=array{lat: float, lng: float, id: int, os_gridref: string}
 * @psalm-type JsonTask=array{type: '0'|'1'|'2'|'3'|'4', distance: float, duration: int, coordinates: list<JsonCoordinate>, gap?: list<JsonCoordinate>}
 * @psalm-type JsonTasks=array{open_distance: JsonTask, out_and_return: ?JsonTask, triangle: ?JsonTask, flat_triangle: ?JsonTask, declared?: JsonTask, complete?: 1}
 * @psalm-type JsonTrack=array{validated: null|0|1, date: string, total_points: int, sets: int, date: string, start_time: int, duration: int, points: int, stats: JsonStats, task: JsonTasks, output: array{js: string, kml: string, earth: string}}
 * @psalm-type JsonPartSet=array{validated: null|0|1, output: string, sets: list<JsonPart>, date: string}
 */
class igc_parser {

    public function exec(int $id, array $data): igcResult|igcPartResult|false {
        $file = root . '/.cache/' . $id . '/track.json';
        chdir(root . '/igc_parser');
        error_handler::debug('IGC Parser call', $data);
        $res = exec("./igc_parser '" . json_encode($data) . "'");
        chdir(root);
        if (!$res) {
            return false;
        }
        file_put_contents($file, $res);
        return self::load_data($id, true);
    }

    public static function load_data(int $id, bool $temp = true): igcResult|igcPartResult|false {
        if ($temp) {
            $file = root . '/.cache/' . $id . '/track.json';
        } else {
            $file = root . '/uploads/flight/' . $id . '/track.json';
        }

        if (!file_exists($file)) {
            return false;
        }
        /** @var JsonTrack|JsonPartSet $data */
        $data = json_decode(file_get_contents($file), true);
        if (is_array($data['sets'])) {
            return new igcPartResult($id, $data);
        } else {
            return new igcResult($id, $data);
        }
    }
}
