<?php

namespace track;

use classes\coordinate_bound;
use classes\geometry;
use classes\lat_lng;
use JetBrains\PhpStorm\Pure;
use model\club;
use model\flight;
use model\glider;
use model\log;
use model\pilot;

class track {

    public $id;
    public bool $temp = false;
    public $track_data;
    /** @var task */
    public task $od;
    public task $or;
    public task $tr;
    public task $ft;
    /** @var  log $log */
    protected log $log;
    /** @var glider club */
    private glider $glider;
    /** @var flight club */
    private flight $flight;
    /** @var pilot club */
    private pilot $pilot;
    /** @var club club */
    private club $club;
    private $log_file;
    /**
     * @var coordinate_bound
     */
    private coordinate_bound $bounds;

    public function __construct($id = null) {
        if ($id === null) {
            $this->temp = true;
            $this->id = time();
        } else {
            $this->id = $id;
        }
        $this->pilot = new pilot();
        $this->club = new club();
        $this->glider = new glider();
        $this->bounds = new coordinate_bound();
        if ($id) {
            if (!is_dir($this->get_file_loc())) {
                mkdir($this->get_file_loc());
            }
            $this->log = new log(log::DEBUG, $this->get_file_loc() . '/info.txt');
        }
    }

    public function get_file_loc($id = null, $temp = null): string {
        if (!isset($id)) {
            $id = $this->id;
        }
        if (!isset($temp)) {
            $temp = $this->temp;
        }
        return root . '/uploads/flight/' . ($temp ? 'temp/' : '') . $id;
    }

    public function set_flight(flight $flight) {
        $this->flight = $flight;
    }

    public function get_dim(): int {
        return $this->track_data->has_height_data() ? 3 : 2;
    }

    #[Pure]
    public function get_duration($formatted = false): bool|string {
        if ($formatted) {
            return date('H:i:s', $this->track_data->duration);
        } else {
            return $this->track_data->duration;
        }
    }

    public function get_task($type) {
        return $this->$type;
    }

    public function get_kml_description(): string {
        return '';
    }

    public function get_split_parts() {
        return $this->track_data->sets;
    }

    #[Pure]
    public function get_season(): bool|int|string {
        $season = $this->get_date('Y');
        if ($this->get_date('n') >= 11) {
            $season++;
        }
        return $season;
    }

    #[Pure]
    public function get_date($format = 'Y/m/d'): bool|string {
        return date($format, $this->get_timestamp());
    }

    #[Pure]
    public function get_timestamp(): bool|int {
        return strtotime($this->track_data->start_time);
    }

    #[Pure]
    public function is_winter(): bool {
        $month = $this->get_date('n');
        return (in_array($month, [1, 2, 12]));
    }

    #[Pure]
    public function get_kmz(): string {
        return $this->get_file_loc() . '/track.kmz';
    }

    #[Pure]
    public function get_kmz_earth(): string {
        return $this->get_file_loc() . '/track_earth.kmz';
    }

    #[Pure]
    public function get_kmz_raw(): string {
        return $this->get_file_loc() . '/track_raw.kmz';
    }

    public function load_track_data(): bool {
        if ($file_path = $this->get_data_file()) {
            $this->log->info("Flight Read");
        } else {
            return false;
        }
        $data = json_decode(file_get_contents($file_path));

        $this->track_data = $data;
        $this->od->load_from_data($data->task->open_distance);
        $this->or->load_from_data($data->task->out_and_return);
        $this->tr->load_from_data($data->task->triangle);

        return true;
    }

    #[Pure]
    public function get_data_file(): bool|string {
        if ($this->id) {
            $loc = $this->get_file_loc() . '/track.json';
            if (file_exists($loc)) {
                return $loc;
            }
        }
        return false;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function set_info() {
        if ($this->flight && $this->flight->fid) {
            $this->flight->lazy_load(['pid', 'gid', 'cid']);
            $this->pilot->do_retrieve_from_id(['name'], $this->flight->pid);
            $this->club->do_retrieve_from_id(['title'], $this->flight->cid);
            $this->glider->do_retrieve_from_id(['name'], $this->flight->gid);
        }
    }

    public function set_task($coordinates): defined_task {
        $points = explode(';', $coordinates);
        $task = new defined_task();
        /**
         * @var $waypoints lat_lng[]
         */
        $waypoints = [];
        foreach ($points as $a) {
            $point = geometry::os_to_lat_long($a);
            $waypoints[] = $point;
        }
        if (count($waypoints) == 3 && $waypoints[0]->get_distance_to($waypoints[2]) < 800) {
            $task->type = 'or';
            $task->title = 'Defined Out & Return';
            $task->ftid = $task::TYPE_OUT_AND_RETURN;
        } else if (count($waypoints) == 4 && $waypoints[0]->get_distance_to($waypoints[3]) < 800) {
            $task->type = 'tr';
            $task->title = 'Defined Triangle';
            $task->ftid = $task::TYPE_TRIANGLE;
        } else {
            $task->type = 'go';
            $task->title = 'Open distance';
            $task->ftid = $task::TYPE_OPEN_DISTANCE;
        }

        for ($i = 0; $i < count($waypoints) - 1; $i++) {
            $task->distance += $waypoints[$i]->get_distance_to($waypoints[$i + 1]);
        }
        return $task;
    }
}
