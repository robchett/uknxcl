<?php

namespace classes;

class lat_lng_bound {

    protected lat_lng $north_east;
    protected lat_lng $south_west;
    public string $code;

    public function __construct(lat_lng $north_east, lat_lng $south_west) {
        $this->north_east = $north_east;
        $this->south_west = $south_west;
    }

    public function contains(lat_lng $lat_lng): bool {
        return $this->north_east->lat() > $lat_lng->lat() && $this->south_west->lat() < $lat_lng->lat() && $this->north_east->lng() > $lat_lng->lng() && $this->south_west->lng() < $lat_lng->lng();
    }
}
 