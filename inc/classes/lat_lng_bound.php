<?php

namespace classes;

use JetBrains\PhpStorm\Pure;

class lat_lng_bound {

    /** @var  lat_lng */
    protected lat_lng $north_east;
    /** @var  lat_lng */
    protected lat_lng $south_west;
    /**
     * @var mixed|string
     */
    private $code;

    public function __construct($north_east, $south_west) {
        $this->north_east = $north_east;
        $this->south_west = $south_west;
    }

    #[Pure]
    public function contains(lat_lng $lat_lng): bool {
        return $this->north_east->lat() > $lat_lng->lat() && $this->south_west->lat() < $lat_lng->lat() && $this->north_east->lng() > $lat_lng->lng() && $this->south_west->lng() < $lat_lng->lng();
    }

    public function extend(lat_lng_bound $bound) {
        //todo write this.
    }

}
 