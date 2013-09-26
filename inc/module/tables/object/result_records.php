<?php
namespace tables;

use html\node;

class result_records extends result {


    function make_table(league_table $data) {
        $html = node::create('div.table_wrapper', [],
            node::create('h3', [], 'Results') .
            node::create('table.results.main', [],
                node::create('thead tr', [],
                    node::create('th', [], 'Type') .
                    node::create('th', [], 'Class') .
                    node::create('th', [], 'Gender') .
                    node::create('th', [], 'Name') .
                    node::create('th', [], 'Score') .
                    node::create('th', [], 'Date')
                ) .
                node::create('tbody', [],
                    $this->get_flights(1, 'Open Distance') .
                    $this->get_flights(3, 'Goal', true) .
                    $this->get_flights(2, 'Out and return (Open)', 0) .
                    $this->get_flights(2, 'Out and return (Defined)', 1) .
                    $this->get_flights(4, 'Triangle (Open)', 0) .
                    $this->get_flights(4, 'Triangle (Defined)', 1)
                )
            )
        );
        return $html;
    }

    function get_flights($type, $title, $defined = null) {
        $html = node::create('tr td.title', ['colspan' => '6'], $title) .
            $this->get_flight($type, 1, 'M') .
            $this->get_flight($type, 5, 'M') .
            $this->get_flight($type, 1, 'F') .
            $this->get_flight($type, 5, 'F') .
            (isset($defined) && $defined ?
                $this->get_flight_defined($type, 1, 'M') .
                $this->get_flight_defined($type, 5, 'M') .
                $this->get_flight_defined($type, 1, 'F') .
                $this->get_flight_defined($type, 5, 'F') : '');
        return $html;
    }

    protected function get_flight($ftid, $class, $gender) {
        $flight = new \flight();
        $flight->do_retrieve(
            array(
                'fid',
                'p.name AS p_name',
                'base_score',
                'date'
            ),
            array(
                'join' => array(
                    'pilot p' => 'p.pid = flight.pid',
                    'glider g' => 'g.gid = flight.gid',
                ),
                'where_equals' => array(
                    'ftid' => $ftid,
                    'g.class' => $class,
                    'p.gender' => $gender

                ),
                'order' => 'base_score DESC'
            )
        );

        $html = '';
        if ($flight->fid) {
            $html .= node::create('tr', [],
                node::create('td', [], 'Distance') .
                node::create('td', [], $class) .
                node::create('td', [], $gender) .
                node::create('td', [], $flight->p_name) .
                node::create('td', [], $flight->base_score) .
                node::create('td', [], $flight->date)
            );
        }
        return $html;
    }

    protected function get_flight_defined($ftid, $class, $gender) {
        $flight = new \flight();
        $flight->do_retrieve(
            array(
                'fid',
                'p.name AS p_name',
                'base_score',
                'date',
                'speed'
            ),
            array(
                'join' => array(
                    'pilot p' => 'p.pid = flight.pid',
                    'glider g' => 'g.gid = flight.gid',
                ),
                'where_equals' => array(
                    'ftid' => $ftid,
                    'g.class' => $class,
                    'p.gender' => $gender

                ),
                'order' => 'speed DESC'
            )
        );
        $html = '';
        if ($flight->fid) {
            $html .= node::create('tr', [],
                node::create('td', [], 'Speed') .
                node::create('td', [], $class) .
                node::create('td', [], $gender) .
                node::create('td', [], $flight->p_name) .
                node::create('td', [], number_format($flight->speed, 2)) .
                node::create('td', [], $flight->date)
            );
        }
        return $html;
    }
}