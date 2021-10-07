<?php

namespace track;

class defined_task extends task {

    public static function create_from_coordinates(string $coordinate_string): self {
        $coords = explode(";", $coordinate_string);
        $coordinates = [];
        foreach ($coords as $gridref) {
            $coordinates[] = ['id' => 0, 'lat' => 0, 'lng' => 0, 'os_gridref' => $gridref];
        }
        return new self([
            'coordinates' => $coordinates,
            'distance' => 0,
            'type' => '0',
            'duration' => 0,
        ]);
    }

}