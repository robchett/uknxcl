<?php
namespace track;

class defined_task extends task {

    public function create_from_coordinates($coordinate_string) {
        $coords = explode(";", $coordinate_string);
        if ($coords) {
            foreach($coords as $gridref) {
                $coord = new \stdClass();
                $coord->os_gridref = $gridref;
                $this->coordinates[] = $coord;
            }
        }
    }

}