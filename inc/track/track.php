<?php
namespace track;

use classes\coordinate_bound;
use classes\get;
use object\club;
use object\flight;
use object\glider;
use object\log;
use object\pilot;

class track {
    
    /** @var  log $log */
    protected $log;
    public $id;
    public $temp = false;
    
    public $track_data;
    
    /** @var glider club */
    private $glider;
    
    /** @var flight club */
    private $flight;
    
    /** @var pilot club */
    private $pilot;

    /** @var club club */
    private $club;
    
    /** @var task */
    public $od, $or, $tr, $ft;
    
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
        if (!is_dir($this->get_file_loc())) {
            mkdir($this->get_file_loc());
        }
        $this->log = new log(log::DEBUG, $this->get_file_loc() . '/info.txt');
    }
    
    public function set_flight(flight $flight) {
        $this->flight = $flight;
    }

    public function cleanup() {
        unset($this->flight);
        unset($this->or);
        unset($this->od);
        unset($this->tr);
    }

    public function get_timestamp() {
        return strtotime($this->track_data->start_time);
    }
    
    public function get_date($format = 'Y/m/d') {
        return date($format, $this->get_timestamp());
    }
    
    public function get_dim() {
        return $this->track_data->has_height_data() ? 3 : 2;
    }
    
    public function get_duration($formatted = false) {
        if ($formatted) {
            return date('H:i:s', $this->track_data->duration);
        } else {
            return $this->track_data->duration;
        }
    }

    public function get_task($type) {
        return $this->$type;
    }
    
    public function get_file_loc($id = null, $temp = null) {
        if (!isset($id)) {
            $id = $this->id;
        }
        if (!isset($temp)) {
            $temp = $this->temp;
        }
        return root . '/uploads/flight/' . ($temp ? 'temp/' : '') . $id;
    }
    
    public function get_kml_description() {
        return '';
    }

    public function set_from_parser(igc_parser $parser) {
        $data['source'] = $file_path;
        $res = exec("/usr/local/bin/igc_parser '" . json_encode($data) . "'");
        $this->coordinate_set = json_decode($res);
        return $this->coordinate_set;
    }
    

    public function get_split_parts() {
        return $this->track_data->sets;
    }
    
    public function get_season() {
        $season = $this->get_date('Y');
        if ($this->get_date('n') >= 11) {
            $season++;
        }
        return $season;
    }

    public function is_winter() {
        $month = $this->get_date('n');
        return (in_array($month, [1, 2, 12]));
    }
    
    public function get_data_file() {
        if ($this->id) {
            $loc = $this->get_file_loc() . '/track.json';
            if (file_exists($loc)) {
                return $loc;
            }
        }
        return false;
    }
    
    public function get_kmz() {
        return $this->get_file_loc() . '/track.kmz';
    }
    
    public function get_kmz_earth() {
        return $this->get_file_loc() . '/track_earth.kmz';
    }
    
    public function get_kmz_raw() {
        return $this->get_file_loc() . '/track_raw.kmz';
    }
    
    public function load_track_data() {
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
}
