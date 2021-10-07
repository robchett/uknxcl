<?php

namespace classes;

use stdClass;

class lat_lng {

    public string $name;
    public float $lng;
    public float $lat;
    public int $ele;

    public function __construct(float $lat, float $lng) {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * @return lat_lng_bound|stdClass
     */
    public function get_grid_cell(): lat_lng_bound|stdClass {
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

    public function get_kml_coordinate(?int $time = null): string {
        if ($time !== null) {
            return sprintf("%8f,%8f,%-5d,%6d ", $this->lng(), $this->lat(), $this->ele(), $time);
        } else {
            return sprintf("%8f,%8f,%-5d ", $this->lng(), $this->lat(), $this->ele());
        }
    }

    public function lng(bool $as_rad = false): float {
        return $this->lng * ($as_rad ? M_PI / 180 : 1);
    }

    public function lat(bool $as_rad = false): float {
        return $this->lat * ($as_rad ? M_PI / 180 : 1);
    }

    public function ele(): int {
        return $this->ele;
    }

    public function sin_lng(): float {
        return sin($this->lng(true));
    }

    public function cos_lng(): float {
        return cos($this->lng(true));
    }

    public function get_distance_to(lat_lng $other): float|int {
        $x = $this->sin_lat() * $other->sin_lat() + $this->cos_lat() * $other->cos_lat() * cos($this->lng(true) - $other->lng(true));
        if (!is_nan($acos = acos($x))) {
            return ($acos * 6371);
        } else {
            return 0;
        }
    }

    public function sin_lat(): float {
        return sin($this->lat(true));
    }

    public function cos_lat(): float {
        return cos($this->lat(true));
    }
}

 