<?php
namespace latest;
use html\node;

class _default_view extends \view {
    public function get_view() {
        $flights = \flight_array::get_all(
            array('flight.*', 'pilot.name', 'pilot.pid'),
            array(
                'join' => array(
                    'pilot' => 'flight.pid = pilot.pid'
                ),
                'where' => '`delayed` = 0 AND personal = 0',
                'limit' => 39,
                'order' => 'fid DESC'
            )
        );
        $wrapper = node::create('div.table_wrapper');
        $wrapper->add_child(node::create('h3', 'Latest'));
        $html = new node('table.results.main', '', array('style' => 'width:700px'));
        $html->add_child(
            node::create('thead')->add_child(
                node::create('tr')->nest(array(
                        node::create('th', 'ID'),
                        node::create('th', 'Pilot'),
                        node::create('th', 'Date Flown'),
                        node::create('th', 'Date Added'),
                        node::create('th', 'Score'),
                        node::create('th', 'Flight Waypoints'))
                )
            )
        );
        $body = new node('tbody');
        //$flights->iterate(function ($flight) use (&$body) {
        /** @var \flight $flight */
        foreach ($flights as $flight) {
            $added = substr($flight->added, 0, 10);
            $body->add_child(node::create('tr')
                    ->nest(array(
                            node::create('td', $flight->fid),
                            node::create('td', $flight->pilot_name),
                            node::create('td', $flight->date),
                            node::create('td', ($added != '0000-00-00' ? $added : 'Unknown')),
                            $flight->to_print(),
                            node::create('td', $flight->coords)
                        )
                    )
            );
        }
        //});
        $html->add_child($body);
        $wrapper->add_child($html);
        return $wrapper->get();

    }
}
