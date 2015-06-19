<?php

class task {

    /**
     * @return int
     */
    public function get_distance() { }

    /**
     * @return string
     */
    public function get_gridref() { }

    /**
     * @return int[]
     */
    public function get_coordinate_ids() { }

    /**
     * @return int
     */
    public function get_duration() { }

    /**
     * @param coordinate_set $set
     *
     * @return bool
     */
    public function completes_task(coordinate_set $set) { }

}