<?php

namespace module\tables\model;

use html\node;
use model\flight;

class result_records extends result
{


    function make_table(league_table $data): string
    {
        return "
        <div id='table_wrapper' class='table_wrapper'>
            <h3>Results</h2>
            <table class='results main'>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Class</th>
                        <th>Gender</th>
                        <th>Name</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    " . $this->get_flights($data, 1, 'Open Distance') . "
                    " . $this->get_flights($data, 3, 'Goal', true) . "
                    " . $this->get_flights($data, 2, 'Out and return (Open)', false) . "
                    " . $this->get_flights($data, 2, 'Out and return (Defined)', true) . "
                    " . $this->get_flights($data, 4, 'Triangle (Open)', false) . "
                    " . $this->get_flights($data, 4, 'Triangle (Defined)', true) . "
                </tbody>
            </table>
        </div>";
    }

    function get_flights(league_table $data, int $type, string $title, bool $defined = false): string
    {
        return "<tr><td class='title' colspan=6><h2>$title</h2></td></tr>" .
            $this->get_flight($data, $type, 1, 1) .
            $this->get_flight($data, $type, 5, 1) .
            $this->get_flight($data, $type, 1, 2) .
            $this->get_flight($data, $type, 5, 2);
    }

    protected function get_flight(league_table $data, int $ftid, int $class, int $gender): string
    {
        if ($flight = flight::get(
            new \classes\tableOptions(
                where_equals: [
                    'flight.ftid'     => $ftid,
                    'flight__glider.class'  => $class,
                    'flight__pilot.gid' => $gender,
                ],
                order: 'base_score DESC',
            )
        )) {
            $g = match ($gender) {
                1 => 'M',
                2 => 'F',
            };
            return "
            <tr>
                <td>Distance</td>
                <td>{$class}</td>
                <td>{$g}</td>
                <td>{$data->getTitle($flight)}</td>
                <td>{$flight->base_score}</td>
                <td>" . date('d/m/Y', $flight->date) . "</td>
            </tr>";
        }
        return '';
    }
}
