<?php
namespace classes;

class lat_lng_bound {

    /** @var  lat_lng */
    protected $north_east;
    /** @var  lat_lng */
    protected $south_west;

    public function __construct($north_east, $south_west) {
        $this->north_east = $north_east;
        $this->south_west = $south_west;
    }

    public function contains(lat_lng $lat_lng) {
        return $this->north_east->lat() > $lat_lng->lat() && $this->south_west->lat() < $lat_lng->lat() && $this->north_east->lng() > $lat_lng->lng() && $this->south_west->lng() < $lat_lng->lng();
    }

    public function extend(lat_lng_bound $bound) {
        //todo write this.
    }

}
 