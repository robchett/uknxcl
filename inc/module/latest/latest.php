<?php

class latest extends core_module {

    public $page = 'latest';

    public function get() {
        $flights = flight_array::get_all(
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
        $wrapper = html_node::create('div.table_wrapper');
        $wrapper->add_child(html_node::create('h3', 'Latest'));
        $html = new html_node('table.results.main', '', array('style' => 'width:700px'));
        $html->add_child(
            html_node::create('thead')->add_child(
                html_node::create('tr')->nest(array(
                        html_node::create('th', 'ID'),
                        html_node::create('th', 'Pilot'),
                        html_node::create('th', 'Date Flown'),
                        html_node::create('th', 'Date Added'),
                        html_node::create('th', 'Score'),
                        html_node::create('th', 'Flight Waypoints'))
                )
            )
        );
        $body = new html_node('tbody');
        //$flights->iterate(function ($flight) use (&$body) {
        /** @var flight $flight */
        foreach ($flights as $flight) {
            $added = substr($flight->added, 0, 10);
            $body->add_child(html_node::create('tr')
                    ->nest(array(
                            html_node::create('td', $flight->fid),
                            html_node::create('td', $flight->pilot_name),
                            html_node::create('td', $flight->date),
                            html_node::create('td', ($added != '0000-00-00' ? $added : 'Unknown')),
                            $flight->to_print(),
                            html_node::create('td', $flight->coords)
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