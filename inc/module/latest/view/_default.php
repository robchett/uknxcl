<?php
namespace module\latest\view;

use classes\view;
use html\node;
use object\flight;

class _default extends \template\html {

    public function get_view() {
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
        $wrapper = node::create('div.table_wrapper', [],
            node::create('h3', [], 'Latest') .
            node::create('table.results.main', ['style' => 'width:700px'],
                node::create('thead', [],
                    node::create('tr', [],
                        node::create('th', [], 'ID') .
                        node::create('th', [], 'Pilot') .
                        node::create('th', [], 'Date Flown') .
                        node::create('th', [], 'Date Added') .
                        node::create('th', [], 'Score') .
                        node::create('th', [], 'Flight Waypoints')
                    )
                ) .
                node::create('tbody', [],
                    $flights->iterate_return(function (flight $flight) use (&$body) {
                            $added = substr($flight->created, 0, 10);
                            return node::create('tr', [],
                                node::create('td', [], $flight->fid) .
                                node::create('td', [], $flight->pilot->name) .
                                node::create('td', [], date('Y-m-d', $flight->date)) .
                                node::create('td', [], ($added != '0000-00-00' ? $added : 'Unknown')) .
                                $flight->to_print() .
                                node::create('td', [], $flight->coords)
                            );
                        }
                    )
                )
            )
        );
        return $wrapper->get();

    }
}
