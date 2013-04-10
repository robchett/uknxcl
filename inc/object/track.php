<?php
/**
 * @property track_point_array track_points
 * @property track_part_array  track_parts
 */
class track {
    public $id;
    public $source;
    public $track_points;
    public $distance_map;
    public $maximum_distance_between_two_points;
    public $track_parts;
    public $c;
    public $maximum_ele = -1000000;
    public $maximum_ele_t = 0;
    public $min_ele = 1000000;
    public $min_ele_t = 0;
    public $maximum_alt = -1000000;
    public $min_alt = 1000000;
    public $maximum_cr = 0;
    public $min_cr = 0;
    public $maximum_speed = 0;
    public $total_dist = 0;
    public $log = 0;
    public $raw = 1;
    public $log_file = "";
    public $calc_od = 1;
    public $calc_or = 1;
    public $calc_tr = 1;
    public $parsed = false;
    public $error = '';
    public $pilot = '';
    public $club = '';
    public $glider = '';
    public $date;
    public $colour = 0;
    public $temp = false;
    public $day = false;
    public $mon = false;
    public $year = false;
    private $generated_graph = false;

    public function __construct() {
        $this->od = new task('Open Distance');
        $this->or = new task('Out and Rerurn');
        $this->tr = new task('Triangle');
        $this->track_parts = new track_part_array();
        $this->track_points = new track_point_array();
    }

    static function split_igc($id, $start, $end) {
        copy(root . '/uploads/track/' . $id . '/track_backup.igc', root . '/uploads/track/' . $id . '/track.igc');
        $file = file(root . '/uploads/track/' . $id . '/track.igc');
        $outFile = fopen(root . '/uploads/track/' . $id . '/track.igc', 'w');
        $i = 0;
        $b_record_count = 0;
        while (isset ($file [$i])) {
            $record = $file [$i];
            if ($record [0] == 'B') {
                if ($b_record_count >= $start && $b_record_count <= $end)
                    fwrite($outFile, $record);
                $b_record_count++;
            } else
                fwrite($outFile, $record);
            $i++;
        }
    }

    public function generate($id) {
        $this->id = $id;
        if ($this->parse_IGC()) {
            $this->calculate();
            $this->generate_output_files();
        } else {
            return false;
        }
    }

    public function parse_IGC() {
        if ($this->id) {
            $loc = $this->get_file_loc() . '/track.igc';
            if (!file_exists($loc)) {
                $loc_old = $this->get_file_loc() . '/Track_log.igc';
                if (!(file_exists($loc_old) && copy($loc_old, $loc) && unlink($loc_old))) {
                    return false;
                }
            }
            $file = file($loc);
            $this->console("Flight Read", $this, 1, 1);
        } elseif ($this->source) {
            $file = file($this->source);
        } else {
            return false;
        }
        foreach ($file as $line) {
            switch (substr($line, 0, 1)) {
                case 'B' :
                    $this->match_b_record($line);
                    break;
                case 'H' :
                    $this->match_h_record($line);
                    break;
            }
        }
        $this->track_parts->last()->finish($this->track_points->last(), $this->track_points->count() - 1);
        if ($this->track_points->count()) {
            $this->parsed = true;
            return true;
        } else {
            return false;
        }
    }

    public function match_b_record($p) {
        $pos = 1;
        $track_point = new track_point ();
        // Extract time data
        $track_point->time = mktime(substr($p, $pos, 2), substr($p, $pos + 2, 2), substr($p, $pos + 4, 2));
        $pos += 6;
        // Extract latitude data
        $lat = round(substr($p, $pos, 2) + substr($p, $pos + 2, 5) / 60000, 6);
        $track_point->lat = (substr($p, $pos + 7, 1) == 'N') ? $lat : -1 * $lat;
        $track_point->sin_lat = sin(M_PI * $track_point->lat / 180);
        $track_point->cos_lat = cos(M_PI * $track_point->lat / 180);
        $pos += 8;
        // Extract longitude data
        $lon = round(substr($p, $pos, 3) + (substr($p, $pos + 3, 5) / 60000), 6);
        $track_point->lon = (substr($p, $pos + 8, 1) == 'E') ? $lon : -1 * $lon;
        $track_point->lonRad = M_PI * $track_point->lon / 180;
        $pos += 9;
        // Get height data
        $track_point->val = (int) substr($p, $pos, 1);
        $pos += 1;
        $track_point->alt = (int) substr($p, $pos, 5);
        $pos += 5;
        $track_point->ele = (int) substr($p, $pos, 5);

        if ($this->track_points->count() == 0) {
            $this->track_points[] = $track_point;
            $this->track_parts[] = new track_part($track_point, 0);
            return;
        }
        $last_point = $this->track_points->last();
        $this->track_points[] = $track_point;
        if ($track_point->time - $last_point->time > 60 || $track_point->time - $last_point->time < 0) {
            $this->track_parts->last()->finish($last_point, $this->track_points->count() - 2);
            $this->track_parts[] = new track_part($track_point, $this->track_points->count() - 1);
        }
    }

    public function match_h_record($p) {
        if (substr($p, 0, 5) == "HFDTE") {
            $this->day = substr($p, 5, 2);
            $this->mon = substr($p, 7, 2);
            $this->year = "20" . substr($p, 9, 2);
            $this->date = "$this->year-$this->mon-$this->day";
            $this->console("Date recorded as : $this->date", $this, 1, 1);
        }
    }

    public function calculate() {
        set_time_limit(0);
        $this->pre_calc();
        $this->get_dist_map();
        if ($this->calc_od) {
            $this->track_open_distance_3tp();
            $this->console("Open Distance Calculated, Dist:{$this->od->get_distance()} Cords={$this->od->get_coordinates()}", $this);
        }
        if ($this->calc_or) {
            $this->track_out_and_return();
            $this->console("Out and Return Calculated, Dist:{$this->or->get_distance()} Cords={$this->or->get_coordinates()}");
        }
        if ($this->calc_tr) {
            $this->track_triangles();
            $this->console("Triange Calculated, Dist:{$this->tr->get_distance()} Cords={$this->tr->get_coordinates()}", $this);
        }
        //$this->set_info(); // TODO set the pilot info.
    }

    public function pre_calc() {
        $this->console('Pre Calculation Setup:');
        $this->trim();
        $this->repair_track();
        $this->get_graph_values();
        $this->get_limits();
        $no = ($this->track_points->count() > 700) ? 700 : $this->track_points->count();
        $choose = $this->track_points->count() / $no;
        $this->console("-> Using $no Track Points");
        for ($i = 0; $i < $no; $i++) {
            $this->c[$i] = $this->track_points[round($i * $choose)];
            $this->c[$i]->id_old = round($i * $choose);
            $this->c[$i]->id = $i;
        }
        $this->nChoosen = sizeof($this->c);
    }

    public function trim() {
        if ($this->track_parts->count() > 1) {
            while (1) {
                if ($this->track_parts->first()->size() < 20) {
                    $this->console("Beginning section ignored, less than 20 points", $this, 1, 1);
                    $this->track_points->remove_first($this->track_parts->first()->size());
                    $this->track_parts->reduce_index($this->track_parts->first()->size());
                    $this->track_parts->remove_first();
                    continue;
                }
                $midPoint = round(($this->track_parts->first()->size()) / 2);
                $coord1 = $this->track_points[$this->track_parts->first()->start_point];
                $coord2 = $this->track_points[$this->track_parts->first()->start_point + $midPoint];
                $coord3 = $this->track_points[$this->track_parts->first()->end_point];
                if ($coord1->get_dist_to($coord2) + $coord2->get_dist_to($coord3) < .200) {
                    $this->console("Beginning section ignored, less than 100m", $this, 1, 1);
                    $this->track_points->remove_first($this->track_parts->first()->size());
                    $this->track_parts->reduce_index($this->track_parts->first()->size());
                    $this->track_parts->remove_first();
                } else break;
            }
            while (1) {
                if ($this->track_parts->last()->size() < 20) {
                    $this->console("End section ignored, less than 20 points", $this, 1, 1);
                    $this->track_points->remove_last($this->track_parts->last()->size());
                    $this->track_parts->remove_last();
                    continue;
                }
                $midPoint = round(($this->track_parts->last()->size()) / 2);
                $coord1 = $this->track_points[$this->track_parts->last()->start_point];
                $coord2 = $this->track_points[$this->track_parts->last()->start_point + $midPoint];
                $coord3 = $this->track_points[$this->track_parts->last()->end_point];
                if ($coord1->get_dist_to($coord2) + $coord2->get_dist_to($coord3) < .200) {
                    $this->console("End section ignored, less than 100m", $this, 1, 1);
                    $this->track_points->remove_last($this->track_parts->last()->size());
                    $this->track_parts->remove_last();
                } else break;
            }
        } else {
            $this->console('1 Part');
        }
    }

    public function repair_track() {
        $previous = $this->track_points->first();
        foreach ($this->track_points as $track_point) {
            if ($this->has_height() && $track_point->ele == 0) {
                //$this->console("Filled in trough  : 0 ele", $this);
                $track_point->ele = $previous->ele;
            }
            if ($this->has_height() && $track_point->ele > $previous->ele + 500) {
                //$this->console("Flattened peak  : {$this->track_points->last()->ele} -> $track_point->ele", $this);
                $track_point->ele = $previous->ele;
            }
            $previous = $track_point;
        }
    }

    public function has_height() {
        if (!isset($this->has_height)) {
            foreach ($this->track_points as $track_point) {
                if ($track_point->ele != 0) {
                    $this->has_height = true;
                    return true;
                }
            }
            $this->has_height = false;
        }
        return $this->has_height;
    }

    public function get_graph_values() {
        if (!$this->generated_graph) {
            $previous = $this->track_points->first();
            foreach ($this->track_points as $track_point) {
                // Calculate climb rate
                if ($this->has_height()) {
                    if ($track_point->time - $previous->time) {
                        $track_point->climbRate = ($track_point->ele - $previous->ele) / ($track_point->time - $previous->time);
                    } else $track_point->climbRate = 0;
                } else
                    $track_point->climbRate = 0;
                // calculate speed
                if ($previous->time !== $track_point->time) {
                    $x = $track_point->get_dist_to($previous);
                    $track_point->speed = round(($x * 1000) / ($track_point->time - $previous->time), 2);
                    $this->total_dist += $x;
                } else
                    $track_point->speed = 0;
                // calculate bearing
                if ($previous->time !== $track_point->time) {
                    $y = sin($track_point->lonRad - $previous->lonRad) * $track_point->cos_lat;
                    $x = $previous->cos_lat * $track_point->sin_lat - $previous->sin_lat * $track_point->cos_lat * cos($track_point->lonRad - $previous->lonRad);
                    $track_point->bearing = atan2($y, $x) * 180 / M_PI;
                } else
                    $track_point->bearing = 0;
                if ($track_point->bearing < 0)
                    $track_point->bearing += 360;
                $previous = $track_point;

            }
        }
        $this->generated_graph = true;
    }

    public function get_limits() {
        $start_time = $this->track_points->first()->time;
        foreach ($this->track_points as $track_point) {
            // Compare heights with max/min
            if ($track_point->ele > $this->maximum_ele) {
                $this->maximum_ele = $track_point->ele;
                $this->maximum_ele_t = $track_point->time - $start_time;
            }
            if ($track_point->ele < $this->min_ele) {
                $this->min_ele = $track_point->ele;
                $this->min_ele_t = $track_point->time - $start_time;
            }
            if ($track_point->alt > $this->maximum_alt) {
                $this->maximum_alt = $track_point->alt;
                $this->maximum_alt_t = $track_point->time - $start_time;
            }
            if ($track_point->alt < $this->min_alt) {
                $this->min_alt = $track_point->alt;
                $this->min_alt_t = $track_point->time - $start_time;
            }
            if ($track_point->climbRate < $this->min_cr) {
                $this->min_cr = $track_point->climbRate;
            }
            if ($track_point->climbRate > $this->maximum_cr) {
                $this->maximum_cr = $track_point->climbRate;
            }
            if ($track_point->speed > $this->maximum_speed) {
                $this->maximum_speed = $track_point->speed;
            }
        }
    }

    public function get_dist_map() {
        foreach ($this->c as $point) {
            for ($key2 = $point->id; $key2 < $this->nChoosen; $key2++) {
                $y = (int) ($point->get_dist_to($this->c[$key2]) * 1000);
                $this->distance_map[$point->id][$key2] = $y;
                $this->distance_map[$key2][$point->id] = $y;
                if ($y > $this->maximum_distance_between_two_points) $this->maximum_distance_between_two_points = $y;
            }
        }
        for ($i = 0; $i < $this->nChoosen - 1; $i++) {
            if ($this->distance_map[$i][$i + 1] > $this->maximum_distance_between_two_points) $this->maximum_distance_between_two_points = $this->distance_map[$i][$i + 1];
        }
        $this->console("Distances between points calculated");
    }

    public function track_open_distance_3tp() {
        for ($key1 = 0; $key1 < $this->nChoosen; $key1++) {
            $this->c[$key1]->bestBack = $this->maximum_bound_index($this->distance_map[$key1], 0, $key1);
            $this->c[$key1]->bestFwrd = $this->maximum_bound_index($this->distance_map[$key1], $key1, $this->nChoosen);
        }
        $this->console("Max legs calculated");
        $maximum_distance_between_two_points = 0;
        $indexes[0] = 0;
        $indexes[1] = 0;
        $indexes[2] = 0;
        $indexes[3] = 0;
        $indexes[4] = 0;
        $rcnt = -1;
        for ($row = 0; $row <= $this->nChoosen - 1; ++$row) {
            $rcnt++;
            $maxF = 0;
            $midF = 0;
            $endF = 0;
            $endB = 0;
            $maxB = 0;
            $midB = 0;
            for ($key2 = 0; $key2 <= $row; ++$key2) {
                if (($this->distance_map[$row][$key2] + $this->c[$key2]->bestBack[0]) > $maxB) {
                    $maxB = $this->distance_map[$row][$key2] + $this->c[$key2]->bestBack[0];
                    $midB = $key2;
                    $endB = $this->c[$key2]->bestBack[1];
                }
            }
            for ($key2 = $row; $key2 < $this->nChoosen; ++$key2) {
                if (($this->distance_map[$row][$key2] + $this->c[$key2]->bestFwrd[0]) > $maxF) {
                    $maxF = $this->distance_map[$row][$key2] + $this->c[$key2]->bestFwrd[0];
                    $midF = $key2;
                    $endF = $this->c[$key2]->bestFwrd[1];
                }
            }
            if ($maxF + $maxB > $maximum_distance_between_two_points) {
                $maximum_distance_between_two_points = $maxF + $maxB;
                $indexes[0] = $endB;
                $indexes[1] = $midB;
                $indexes[2] = $row;
                $indexes[3] = $midF;
                $indexes[4] = $endF;
            }
        }
        $this->od->set($this->get_list($indexes));
    }

    public function maximum_bound_index($array, $from, $to) {
        $index = $from;
        for ($i = $from; $i < $to; ++$i) {
            if ($array[$index] < $array[$i]) {
                $index = $i;
            } else $i += (int) (($array[$index] - $array[$i]) / $this->maximum_distance_between_two_points);

        }
        return array($array[$index], $index);
    }

    public function get_list($indexes) {
        $list = array();
        foreach ($indexes as $index) {
            $list[] = $this->track_points[$index];
        }
        return $list;
    }

    function track_out_and_return() {
        $maximum_distance_between_two_points = 0;
        $indexes[0] = 0;
        $indexes[1] = 0;
        $indexes[2] = 0;
        for ($row = 0; $row < $this->nChoosen; ++$row) {
            $minLeg = 800;
            for ($col = $this->nChoosen - 1; $col > $row + 2; --$col) {
                if ($this->distance_map[$row][$col] > $minLeg) {
                    $col -= $step = (int) (($this->distance_map[$row][$col] - $minLeg) / $this->maximum_distance_between_two_points);
                    continue;
                }
                $x = $this->furthest_between($row, $col);
                if (($this->distance_map[$row][$x] + $this->distance_map[$x][$col]) - $this->distance_map[$row][$col] > $maximum_distance_between_two_points) {
                    $maximum_distance_between_two_points = $this->distance_map[$row][$x] + $this->distance_map[$x][$col];
                    $indexes[0] = $row;
                    $indexes[2] = $col;
                    $indexes[1] = $x;
                }
                $minLeg = $this->distance_map[$row][$col];
            }
        }
        $this->or->set($this->get_list($indexes));
    }

    public function furthest_between($start, $end) {
        $max = $start;
        for ($x = $start; $x < $end; $x++) {
            if ($this->distance_map[$start][$x] > $this->distance_map[$max][$start])
                $max = $x;
            else $x += (int) (($this->distance_map[$max][$start] - $this->distance_map[$start][$x]) / $this->maximum_distance_between_two_points);
        }
        return $max;
    }

    function track_triangles() {
        $closest_end = 0;
        $maximum_distance_between_two_points = 0;
        $indexes[0] = 0;
        $indexes[1] = 0;
        $indexes[2] = 0;
        $indexes[3] = 0;
        $gap[0] = 0;
        $gap[1] = 0;
        $minleg = 800;
        // for each entry in the dist table (moving forward)
        for ($row = 0; $row < $this->nChoosen; ++$row) {
            for ($col = $this->nChoosen - 1; $col > $row && $col > $closest_end; --$col) {
                // if the distance between the points is < 800m
                if ($this->distance_map[$row][$col] > 800) {
                    $col -= (int) (($this->distance_map[$row][$col] - 800) / $this->maximum_distance_between_two_points);
                    continue;
                }
                for ($x = $row; $x <= $col - 2; ++$x) {
                    if ($this->distance_map[$row][$x] < $minleg) {
                        $x += (int) (($minleg - $this->distance_map[$row][$x]) / $this->maximum_distance_between_two_points);
                        continue;
                    }
                    for ($y = $row + 1; $y <= $col - 1; ++$y) {
                        if ($this->distance_map[$x][$y] < $minleg) {
                            $y += (int) (($minleg - $this->distance_map[$x][$y]) / $this->maximum_distance_between_two_points);
                            continue;
                        }
                        for ($z = max($row + 2, $closest_end); $z <= $col; ++$z) {
                            if ($this->distance_map[$y][$z] < $minleg) {
                                $z += (int) (($minleg - $this->distance_map[$y][$z]) / $this->maximum_distance_between_two_points);
                                continue;
                            }
                            if ($this->distance_map[$x][$z] < $minleg) {
                                $z += (int) (($minleg - $this->distance_map[$x][$z]) / $this->maximum_distance_between_two_points);
                                continue;
                            }
                            if (($d = ($this->distance_map[$x][$y] + $this->distance_map[$y][$z] + $this->distance_map[$z][$x])) > $maximum_distance_between_two_points && min($this->distance_map[$x][$y], $this->distance_map[$y][$z], $this->distance_map[$z][$x]) > (0.28 * $d)) {
                                $maximum_distance_between_two_points = $d;
                                $indexes[0] = $x;
                                $indexes[1] = $y;
                                $indexes[2] = $z;
                                $indexes[3] = $x;
                                $gap[0] = $row;
                                $gap[1] = $col;
                                $minleg = max(1200, $d * 0.28);
                            }
                        }
                    }
                }
                $closest_end = $col;
                $col = 0;
                //$row=$this->nChoosen;
            }
        }
        $this->tr->set($this->get_list($indexes));

    }

    public function generate_output_files() {
        $this->generate_js();
        $this->generate_kml();
        $this->generate_kml_earth();
    }

    public function generate_js() {
        $out = array();
        foreach ($this->track_points as $a) {
            $time = $a->time - $this->track_points->first()->time;
            $out[] = sprintf("[%f,%f,%d,%d,%.3f,%.3f]", $a->lat, $a->lon, $a->ele, $time, $a->climbRate, $a->speed, $a->bearing);
        }
        $coordinates = implode(',', $out);
        $track = new stdClass();
        $track->id = $this->id;
        $track->StartT = 0;
        $track->EndT = (isset($this->total_time) ? $this->total_time : 0);
        $track->od_score = $this->od->get_distance();
        $track->od_time = $this->od->get_time();
        $track->or_score = $this->or->get_distance();
        $track->or_time = $this->or->get_time();
        $track->tr_score = $this->tr->get_distance();
        $track->tr_time = $this->tr->get_time();

        $track_inner = new stdClass();
        $track_inner->drawGraph = 1;
        $track_inner->pilot = $this->pilot;
        $track_inner->colour = "FF0000";
        $track_inner->maxEle = $this->maximum_ele;
        $track_inner->minEle = $this->min_ele;
        $track_inner->maximum_cr = $this->maximum_cr;
        $track_inner->min_cr = $this->min_cr;
        $track_inner->maximum_speed = $this->maximum_speed;
        $track_inner->total_dist = $this->total_dist;
        $track_inner->av_speed = (isset($this->average_speed_over_track) ? $this->average_speed_over_track : 0);
        $track_inner->coords = $coordinates;
        $track->track = array($track_inner);

        fwrite(fopen($this->get_file_loc() . '/Track.js', 'w'), json_encode($track));
        fwrite(fopen($this->get_file_loc() . '/info.txt', 'w'), $this->log_file);
    }

    public function  generate_kml($external = false) {
        $kml = new kml();
        if (!$external) {
            $kml->set_gradient_styles();
        }
        $kml->add($this->get_kml_description());
        if (!$external) {
            $kml->add($this->get_meta_linestring());
            $kml->add($this->od->get_kml_track('FF0000', 'Open Distance'));
            $kml->add($this->or->get_kml_track('FF0000', 'Out And Return'));
            $kml->add($this->tr->get_kml_track('FF0000', 'FAI Triangle'));

        }
        if (0) {
            $kml->add($this->get_colour_by_height());
        }
        if ($external) {
            $kml->add($this->get_kml_time_aware_points());
        }
        if (!$external) {
            $outFile = fopen($this->get_file_loc() . '/Track.kml', 'w');
            fwrite($outFile, $kml->compile());
        }
        return $kml->compile(1);
    }

    public function get_kml_description() {
        return '
      <name>Flight ' . $this->id . '</name>
      <description>
          <![CDATA[
            <pre>
Flight statistics
Flight #             ' . $this->id . '
Pilot                ' . $this->pilot . '
Club                 ' . $this->club . '
Glider               ' . $this->glider . '
Date                 ' . $this->date . '
Start/finish         ' . $this->start_time(true) . '/' . $this->end_time(true) . '
Duration             ' . $this->duration(true) . '
Max./min. height     ' . $this->maximum_ele / $this->min_ele . 'm
OD Score / Time      ' . $this->od->get_formatted_time() . '
OR Score / Time      ' . $this->or->get_formatted_time() . '
TR Score / Time      ' . $this->tr->get_formatted_time() . '
            </pre>
          ]]>
      </description>';
    }

    public function start_time($formatted = false) {
        if ($formatted) {
            return date('H:i:s', $this->track_points->first()->time);
        } else {
            return $this->track_points->first()->time;
        }
    }

    public function end_time($formatted = false) {
        if ($formatted) {
            return date('H:i:s', $this->track_points->last()->time);
        } else {
            return $this->track_points->last()->time;
        }
    }

    public function duration($formatted = false) {
        if ($formatted) {
            return date('H:i:s', $this->track_points->last()->time - $this->track_points->first()->time);
        } else {
            return $this->track_points->last()->time - $this->track_points->first()->time;
        }
    }

    private function get_meta_linestring() {
        $output = '<Placemark>';
        $output .= $this->get_time_meta_data();
        $output .= $this->get_kml_linestring();
        $output .= '</Placemark>';
        return $output;
    }

    public function get_time_meta_data($start = 0, $end = 0) {
        $cnt = 0;
        $num = count($this->track_points);
        if ($end == 0) {
            $end = $num - 1;
        }
        $output = "
      <Metadata src='UKNXCL' v='0.9' type='track'>
        <SecondsFromTimeOfFirstPoint>\n";
        for ($i = $start; $i < $num; $i++, $cnt++) {
            if ($cnt == 5) {
                $output .= "\n";
                $cnt = 0;
            }
            $output .= $this->track_points[$i]->time - $this->track_points->first()->time . " ";
        }
        $output .= '
        </SecondsFromTimeOfFirstPoint>
      </Metadata>';
        return $output;

    }

    public function get_kml_linestring($start = 0, $end = 0, $altitude = 'absolute', $extrude = 0) {
        $num = count($this->track_points);
        if ($end == 0) $end = $num - 1;
        $cnt = 0;
        if (!$start)
            $start = $this->track_points->first_index();
        $output = '';
        $output .= '
    <LineString>
        <altitudeMode>' . $altitude . '</altitudeMode>
        <extrude>' . $extrude . '</extrude>
        <coordinates>';
        for ($i = $start; $i < $num; $i++, $cnt++) {
            if ($cnt == 5) {
                $output .= "\n";
                $cnt = 0;
            }
            $output .= $this->track_points[$i]->get_kml_coordinate();
        }
        $output .= '
        </coordinates>
  </LineString>';
        return $output;
    }

    public function get_kml_time_aware_points($col = false) {
        $output = '';
        $tot = $this->track_points->count();
        for ($i = 0; $i < $tot - 1; $i++) {
            $current_level = floor(($this->track_points[$i]->time - $this->track_points->first()->time) * 16 / $this->duration());
            $output .= '<Placemark>';
            if (!$col)
                $output .= '<styleUrl>#S' . $current_level . '</styleUrl>';
            else
                $output .= '<Style><LineStyle><color>FF' . $col . '</color><width>2</width></LineStyle></Style>';
            $output .= kml::get_timespan($this->track_points[$i]->time, $this->track_points[$i + 1]->time) . '
			<LineString>
				<altitudeMode>absolute</altitudeMode>
				<coordinates>
					' . $this->track_points[$i]->get_kml_coordinate() . ' ' . $this->track_points[$i + 1]->get_kml_coordinate() . '
				</coordinates>
			</LineString>
		</Placemark>';
        }
        return $output;
    }

    public function generate_kml_earth() {
        $kml = new kml();

        $kml->set_folder_styles();
        $kml->set_gradient_styles(1);
        $kml->set_animation_styles(1);

        $kml->get_kml_folder_open('Track', 1, 'radio', 1);

        $kml->get_kml_folder_open('Colour By Height', 1, 'hideChildren', 0);
        $kml->add($this->get_colour_by($this->min_ele, $this->maximum_ele, 'ele'));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_open('Colour By Ground Speed', 0, 'hideChildren', 0);
        $kml->add($this->get_colour_by(0, $this->maximum_speed, 'speed'));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_open('Colour By Climb', 0, 'hideChildren', 0);
        $kml->add($this->get_colour_by($this->min_cr, $this->maximum_cr, 'climbRate'));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_open('Colour By Time', 0, 'hideChildren', 0);
        $kml->add($this->get_colour_by($this->track_points->first()->time, $this->track_points->last()->time, 'time', 0));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_close();
        $kml->get_kml_folder_open('Shadow', 1, 'radio');

        $kml->get_kml_folder_open('None', 0, 'hideChildren', 0);
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_open('Standard', 1, 'hideChildren', 0);
        $kml->add(kml::create_linestring('shadow', $this->track_points->subset(), 'clampToGround'));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_open('Extrude', 0, 'hideChildren', 0);
        $kml->add(kml::create_linestring('shadow', $this->track_points->subset(), 'absolute', 1));
        $kml->get_kml_folder_close();

        $kml->get_kml_folder_close();

        /*      $kml->get_kml_folder_open('Colour By Time', 0);
                $kml->add($this->get_kml_time_aware_points();
                $kml->get_kml_folder_close();*/

        $kml->get_kml_folder_open('Task', 0, '', 0);
        $kml->add($this->od->get_kml_track('FF0000', 'Open Distance'));
        $kml->add($this->or->get_kml_track('00FF00', 'Out And Return'));
        $kml->add($this->tr->get_kml_track('0000FF', 'FAI Triangle'));
        $kml->get_kml_folder_close();


        $kml->get_kml_folder_open('Animation', 0, 'hideChildren', 0);
        $kml->add($this->get_animation());
        $kml->get_kml_folder_close();
        fwrite(fopen($this->get_file_loc() . '/Track_Earth.kml', 'w'), $kml->compile());
    }

    public function get_colour_by($min, $max, $value, $scale = 1) {
        $this->get_graph_values();
        $output = '';
        $var = $max - $min;
        $last_level = floor(($this->track_points[0]->$value - $min) * 16 / $var);

        $coords = array();
        foreach ($this->track_points as $out1) {
            $coords[] = $out1;
            $current_level = floor(($out1->$value - $min) * 16 / $var);
            if ($current_level != $last_level) {
                $output .= kml::create_linestring('#S' . $last_level, $coords);
                $coords = array();
                $coords[] = $out1;
                $last_level = $current_level;
            }
        }
        if (!empty($coords))
            $output .= kml::create_linestring('#S' . $current_level, $coords);
        if ($scale)
            $output .= kml::get_scale($min, $max);
        return $output;
    }

    public function get_animation() {
        $xml = '';
        $tot = $this->track_points->count();
        for ($i = 0; $i < $tot - 1; $i++) {
            $bearing = floor($this->track_points[$i]->bearing / 5) * 5;
            $xml .= '<Placemark>';
            $xml .= '<styleUrl>#A' . $this->colour . $bearing . '</styleUrl>';
            $xml .= kml::get_timespan($this->track_points[$i]->time, $this->track_points[$i + 1]->time);
            $xml .= $this->track_points[$i]->get_kml_point();
            $xml .= '</Placemark>';
        }
        return $xml;
    }

    public function get_number_of_parts() {
        return count($this->track_parts);
    }

    public function create_from_upload($current_loc = '') {
        $dir = $this->get_file_loc();
        // Clear the directory if it exists
        if (!file_exists($dir)) mkdir($dir);
        else {
            $files = glob($dir . '*', GLOB_MARK);
            foreach ($files as $file) {
                unlink($file);
            }
        }
        if ($this->temp) {
            move_uploaded_file($_FILES ["file"] ["tmp_name"], $dir . '/track.igc');
        } else {
            copy($current_loc, $dir . '/track.igc');
        }
        copy($dir . '/track.igc', $dir . '/track_backup.igc');
    }

    public function get_file_loc() {
        if (isset($this->temp) && $this->temp) {
            return root . '/uploads/track/temp/' . $this->id;
        }
        return root . '/uploads/track/' . $this->id;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function set_source($id) {
        $this->source = $id;
    }

    public function enable_logging($bool) {
        $this->log = $bool;
    }

    public function enable_raw($bool) {
        $this->raw = $bool;
    }

    public function check_date() {
        $current_time = date("Y-m-d");
        $closure_time = date("Y-m-d", time() - (31 * 24 * 60 * 60));
        if ($this->date >= $closure_time && $this->date <= $current_time) {
            $this->console("Date is within 1 month");
            return true;
        } else {
            $this->console("Date is outside of 1 month");
            return false;
        }
    }

    public function console($str) {
        if (!$this->raw) {
            echo "<script language='javascript' type='text/javascript'>window.top.window.console_log(\"$str\")</script>";
            echo str_repeat(' ', 1024 * 64) . "\n";
            flush();
        }
        $this->log_file .= "$str\r\n";
    }

    public function getTime($time) {
        $time -= mktime(0, 0, 0);
        return date('H:i:s', $time);
    }

    public function mod60000($a) {
        return round($a / 60000, 6);
    }

    public function set_info() {
        $result = db::query("SELECT pilot.name AS Pilot,club.name AS Club,G_CLASS AS Class,glider.name AS Glider,Score,pid,cid,gid FROM flight
        LEFT JOIN glider ON flight.gid=glider.gid
        LEFT JOIN club ON flight.cid=club.cid
        LEFT JOIN pilots ON flight.pid=pilots.pid
        WHERE flight.ID=$this->id LIMIT 1"
        );
        $row = db::fetch($result);
        $this->pilot = $row['Pilot'];
        $this->pid = $row['pid'];
        $this->club = $row['Club'];
        $this->cid = $row['cid'];
        $this->glider = $row['Glider'];
        $this->gid = $row['gid'];
        $this->score = $row['Score'];
    }

    public function get_season() {
        $season = $this->year;
        if ($this->mon >= 11)
            $season++;
        return $season;
    }

    public function get_winter() {
        return ($this->mon == 1 || $this->mon == 2 || $this->mon == 12);
    }

    public function get_dim() {
        return (($this->maximum_alt != $this->min_alt) || ($this->maximum_ele != $this->min_ele)) ? 3 : 2;
    }

    public function generate_kml_comp_earth() {
        $kml = new kml();
        $kml->get_kml_folder_open($this->name, 1, 'hideChildren');
        $kml->add($this->get_kml_time_aware_points(get::kml_colour($this->colour)));
        $kml->get_kml_folder_close();
        return $kml->compile(true);
    }

    public function generate_kml_comp() {
        $output = '';
        $output .= "\n\t" . '<Placemark>
        <name>' . $this->name . '</name>
        <description><![CDATA[
        <pre>
Flight statistics
Pilot                ' . $this->name . '
Date                 ' . $this->date . '
Start/finish         ' . $this->start_time(true) . '-' . $this->end_time(true) . '
Duration             ' . $this->duration(true) . '
Max./min. height     ' . $this->maximum_ele . '/' . $this->maximum_ele . 'm
            </pre>]]>
        </description>
        <Style>
          <LineStyle>
            <color>FF' . get::kml_colour($this->colour) . '</color>
            <width>2</width>
          </LineStyle>
        </Style>';
        $output .= $this->get_kml_linestring();
        $output .= "\n\t" . '</Placemark>';
        return $output;

    }
}

class track_point {
    public $id = 0;
    public $time = 0;
    public $lat = 0;
    public $lon = 0;
    public $val = 0;
    public $alt = 0;
    public $ele = 0;
    public $sin_lat = 0;
    public $cos_lat = 0;
    public $lonRad = 0;
    public $bestBack;
    public $bestFwrd;
    public $climbRate = 0;
    public $speed = 0;

    public function get_dist_to(track_point $b) {
        $x = $this->sin_lat * $b->sin_lat +
            $this->cos_lat * $b->cos_lat * cos($this->lonRad - $b->lonRad);
        return (acos($x) * 6371);
    }

    public function get_time_to(track_point $b) {
        return $b->time - $this->time;
    }

    public function get_kml_point() {
        $xml = '<Point><altitudeMode>absolute</altitudeMode><coordinates>' . $this->get_kml_coordinate() . '</coordinates></Point>';
        return $xml;
    }

    public function get_kml_coordinate($time = null) {
        if ($time !== null)
            return sprintf("%8f,%8f,%-5d,%6d ", $this->lon, $this->lat, $this->ele, $time);
        else
            return sprintf("%8f,%8f,%-5d ", $this->lon, $this->lat, $this->ele);
    }

    public function get_js_coordinate($time = null) {
        $coord = array($this->lat, $this->lon, $this->ele);
        if ($time !== null)
            $coord[] = $time;
        return $coord;
    }

    public function get_coordinate() {
        return file_convert::LatLongToOSGrid(($this->lat), ($this->lon));
    }
}

class track_part {
    public $start_point;
    public $start;
    public $end_point;
    public $end;
    public $time;

    public function __construct(track_point $point, $pos) {
        $this->start = $point;
        $this->start_point = $pos;
    }

    public function size() {
        return $this->end_point - $this->start_point + 1;
    }

    public function finish(track_point $point, $pos) {
        $this->end_point = $pos;
        $this->end = $point;
    }

    public function get_time() {
        if (!isset($this->time)) {
            $this->start->get_time_to($this->end);
        }
        return $this->time;
    }

    public function reduce_index($int) {
        $this->start_point -= $int;
        $this->end_point -= $int;
    }
}

class task {
    /** @var track_point_array */
    public $waypoints;
    public $title;
    public $timestamp;
    public $distance;
    public $coordinates;

    public function __construct($title = '') {
        $this->waypoints = new track_point_array();
        $this->title = $title;
    }

    public function set($indexes) {
        $this->distance = null;
        foreach ($indexes as $track_point) {
            $this->waypoints[] = $track_point;
        }
        $this->timestamp = $this->waypoints->last()->time - $this->waypoints->first()->time;
    }

    public function get_coordinates() {
        return $this->waypoints->get_coordinates(range(0, $this->waypoints->count() - 1));
    }

    public function get_time() {
        return $this->timestamp;
    }

    public function get_distance( $dp = 10) {
        if (!isset($this->distance)) {
            $this->distance = $this->waypoints->get_distance();
        }
        return number_format($this->distance, $dp);
    }

    public function get_kml_track($colour, $title = '') {
        $coordinates = $this->waypoints->get_kml_coordinates();

        $output = '
<Placemark>
    <visibility>0</visibility>
    <name>' . $title . '</name>
    <description>
        <![CDATA[
        <pre>
            ' . $title . '
            Duration             ' . $this->get_formatted_time() . '
            Score                ' . $this->distance . 'km
        </pre>
        ]]>
    </description>
    <Style>
        <LineStyle>
            <color>FF' . $colour . '</color>
            <width>2</width>
        </LineStyle>
    </Style>
    <LineString>
        <coordinates>
            ' . implode(' ', $coordinates) . '
        </coordinates>
    </LineString>
</Placemark>';

        return $output;
    }

    public function get_formatted_time() {
        return date('H:i:s', $this->timestamp);
    }


}

class track_part_array extends object_array {

    /**
     * @return track_part
     */
    public function last() {
        return parent::last();
    }

    /**
     * @return track_part
     */
    public function first() {
        return parent::first();
    }

    public function reduce_index($int) {
        for ($i = 0; $i < $this->count(); $i++)
            $this->offsetGet($i)->reduce_index($int);
    }
}

class track_point_array extends object_array {

    /**
     * @return track_point
     */
    public function last() {
        return parent::last();
    }

    /**
     * @return track_point
     */
    public function first() {
        return parent::first();
    }

    /**  @return string */
    public function get_coordinates(array $indexes) {
        $coordinates = '';
        foreach ($indexes as $index) {
            $coordinates .= $this[$index]->get_coordinate();
        }
        return $coordinates;
    }

    public function get_distance() {
        $distance = 0;
        if ($this->count()) {
            foreach (range(0, $this->count() - 2) as $index) {
                $distance += $this[$index]->get_dist_to($this[$index + 1]);
            }
        }
        return $distance;
    }

    public function get_kml_coordinates() {
        $coordinates = array();
        if ($this->count()) {
            foreach (range(0, $this->count() - 1) as $index) {
                $coordinates[] = $this[$index]->get_kml_coordinate();
            }
        }
        return $coordinates;
    }
}

class object_array extends ArrayObject {

    private $first_index = 0;

    public function remove_first($int = 1) {
        parent::__construct($this->subset($int));
    }

    public function subset($start = 0, $end = null) {
        $sub = array();
        if ($end == null || $end < $start)
            $end = $this->count();
        for ($i = $start; $i < $end; $i++) {
            $sub[] = $this[$i];
        }
        return $sub;
    }

    public function remove_last($int = 0) {
        if ($int) {
            for ($i = 0; $i < $int; $i++)
                $this->remove_last();
        } else {
            $this->offsetUnset($this->count() - 1);
        }
    }

    public function last() {
        return $this[$this->count() - 1];
    }

    public function first() {
        return $this[0];
    }

    public function first_index() {
        return $this->first_index;
    }
}
