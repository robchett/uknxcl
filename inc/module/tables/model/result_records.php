<?php

namespace module\tables\model;

use html\node;
use model\flight;

class result_records extends result {


    function make_table(league_table $data): string {
        return node::create('div#table_wrapper.table_wrapper', [],
            node::create('h3', [], 'Results') .
            node::create('table.results.main', [],
                node::create('thead tr', [],
                    "<th>Type</th><th>Class</th><th>Gender</th><th>Name</th><th>Score</th><th>Date</th>"
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
    }

    function get_flights($type, $title, $defined = null): string {
        return node::create('tr td.title', ['colspan' => '6'], '<h2>' . $title . '</h2>') .
            $this->get_flight($type, 1, 'M') .
            $this->get_flight($type, 5, 'M') .
            $this->get_flight($type, 1, 'F') .
            $this->get_flight($type, 5, 'F') .
            (isset($defined) && $defined ?
                $this->get_flight_defined($type, 1, 'M') .
                $this->get_flight_defined($type, 5, 'M') .
                $this->get_flight_defined($type, 1, 'F') .
                $this->get_flight_defined($type, 5, 'F') : '');
    }

    protected function get_flight($ftid, $class, $gender): string {
        $flight = new flight();
        if ($flight->do_retrieve(
            [
                'fid',
                'p.name AS p_name',
                'base_score',
                'date',
            ],
            [
                'join'         => [
                    'pilot p'  => 'p.pid = flight.pid',
                    'glider g' => 'g.gid = flight.gid',
                ],
                'where_equals' => [
                    'ftid'     => $ftid,
                    'g.class'  => $class,
                    'p.gender' => $gender,

                ],
                'order'        => 'base_score DESC',
            ]
        )) {
            return node::create('tr', [],
                "<td>Distance</td><td>{$class}</td><td>{$gender}</td><td>{$flight->p_name}</td><td>{$flight->base_score}</td>" .
                node::create('td', [], date('d/m/Y', $flight->date))
            );
        }
        return '';
    }

    protected function get_flight_defined($ftid, $class, $gender): string {
        $flight = new flight();
        if ($flight->do_retrieve(
            [
                'fid',
                'p.name AS p_name',
                'base_score',
                'date',
                'speed',
            ],
            [
                'join'         => [
                    'pilot p'  => 'p.pid = flight.pid',
                    'glider g' => 'g.gid = flight.gid',
                ],
                'where_equals' => [
                    'ftid'     => $ftid,
                    'g.class'  => $class,
                    'p.gender' => $gender,

                ],
                'order'        => 'speed DESC',
           ]
        )) {
            return node::create('tr', [],
                "<td>Speed</td><td>{$class}</td><td>{$gender}</td><td>{$flight->p_name}</td>" .
                node::create('td', [], number_format($flight->speed, 2)) .
                node::create('td', [], date('d/m/Y', $flight->date))
            );
        }
        return '';
    }
}