<?php

namespace classes;

use JetBrains\PhpStorm\Pure;
use stdClass;

class lat_lng {

    public string $name;
    public float $lng;
    public $ele;
    private $lat;

    public function __construct($lat, $lng) {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * @return lat_lng_bound|stdClass
     */
    public function get_grid_cell(): lat_lng_bound|stdClass {
        /** @var lat_lng_bound $cell */
        foreach (OS::cells() as $cell) {
            if ($cell->contains($this)) {
                return $cell;
            }
        }
        $bound = new stdClass();
        $bound->code = 'N/A';
        return $bound;
    }

    public function get_coordinate(): string {
        return geometry::lat_long_to_os($this);
    }

    #[Pure]
    public function get_kml_coordinate($time = null): string {
        if ($time !== null) {
            return sprintf("%8f,%8f,%-5d,%6d ", $this->lng(), $this->lat(), $this->ele(), $time);
        } else {
            return sprintf("%8f,%8f,%-5d ", $this->lng(), $this->lat(), $this->ele());
        }
    }

    /**
     * @param bool $as_rad *
     *
     * @return float|int
     */
    #[Pure]
    public function lng($as_rad = false): float|int {
        return $this->lng * ($as_rad ? M_PI / 180 : 1);
    }

    /**
     * @param bool $as_rad *
     *
     * @return float|int
     */
    #[Pure]
    public function lat($as_rad = false): float|int {
        return $this->lat * ($as_rad ? M_PI / 180 : 1);
    }

    /**
     * @return float
     */
    public function ele(): float {
        return $this->ele;
    }

    #[Pure]
    public function sin_lng(): float {
        return sin($this->lng(true));
    }

    #[Pure]
    public function cos_lng(): float {
        return cos($this->lng(true));
    }

    #[Pure]
    public function get_distance_to(lat_lng $other): float|int {
        $x = $this->sin_lat() * $other->sin_lat() + $this->cos_lat() * $other->cos_lat() * cos($this->lng(true) - $other->lng(true));
        if (!is_nan($acos = acos($x))) {
            return ($acos * 6371);
        } else {
            return 0;
        }
    }

    #[Pure]
    public function sin_lat(): float {
        return sin($this->lat(true));
    }

    #[Pure]
    public function cos_lat(): float {
        return cos($this->lat(true));
    }
}

 