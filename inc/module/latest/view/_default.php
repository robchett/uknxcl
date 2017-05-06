<?php
namespace module\latest\view;

use object\flight;
use traits\twig_view;

class _default extends \template\html {
    use twig_view;

    function get_template_data() {
        $flights = flight::get_all(
            ['flight.*', 'pilot.name', 'pilot.pid'],
            [
                'join' => [
                    'pilot' => 'flight.pid = pilot.pid'
                ],
                'where' => '`delayed` = 0 AND personal = 0',
                'limit' => 40,
                'order' => 'fid DESC'
            ]
        );
        return [
            'flights' => $flights->getArrayCopy()
        ];
    }
}
