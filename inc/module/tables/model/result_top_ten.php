<?php

namespace module\tables\model;

use classes\tableOptions;
use html\node;
use model\flight;
use model\flight_type;

class result_top_ten extends result {

    function make_table(league_table $data): string {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            [$where, $parameters] = $data->get_sql();
            $where[] = 'personal = 0';
            $where[] = 'flight.ftid = ' . $i;
            $flights = flight::get_all(new tableOptions(
                where: implode(" AND ", $where),
                order: 'score DESC',
                limit: "10",
                parameters: $parameters,
            ));
            $count = 0;
            $html .= node::create('div.table_wrapper', [],
                node::create('h3.heading', [], flight_type::get_type($i) . ' - ' . $data->year_title) .
                node::create('table.main.results', [],
                    node::create('thead tr', [],
                        node::create('th', ['style' => 'width:20px'], 'Pos') .
                        node::create('th', ['style' => 'width:100px'], ($data->class_table_alias == 'p' ? 'Name' : 'Glider')) .
                        node::create('th', ['style' => 'width:70px'], ($data->class_table_alias == 'p' ? 'Club' : 'Manufacturer')) .
                        ($data->class_table_alias == 'p' ? node::create('th', ['style' => 'width:100px'], 'Glider') : '') .
                        node::create('th', ['style' => 'width:58px'], 'Score') .
                        node::create('th', ['style' => 'width:300px'], 'Flight Waypoints')
                    ) .
                    $flights->reduce(
                        /** @psalm-suppress all */
                        function (string $acc, flight $flight) use ($data, &$count): string { 
                        $count++;
                        return "$acc
                        <tr>
                            <td>$count</td>
                            <td>{$data->getTitle($flight)}</td>
                            <td>{$data->getSubTitle($flight)}</td>
                            " . ($data->class_table_alias == 'p' ? "<td>{$flight->glider->name}</td>" : '') . "
                            {$flight->to_print()}
                            <td>{$flight->coords}</td>
                        </tr>";
                    }, '')                       
                )
            );
        }
        return '<div id="table_wrapper">' . $html . '</div>';
    }
}