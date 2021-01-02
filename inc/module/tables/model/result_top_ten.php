<?php

namespace module\tables\model;

use html\node;
use model\flight;
use model\flight_type;

class result_top_ten extends result {

    function make_table(league_table $data): string {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $where = $data->where;
            $where[] = 'personal = 0';
            $where[] = 'ftid = ' . $i;
            $flights = flight::get_all(
                [
                    'fid',
                    'flight.pid',
                    'flight.gid',
                    $data->class_table_alias . '.' . $data->class_primary_key . ' AS ClassID',
                    $data->class_table_alias . '.name AS p_name',
                    $data->S_alias . '.title AS c_name',
                    'g.class AS class',
                    'g.name AS g_name',
                    'coords',
                    'g.mid',
                    'g.kingpost',
                    'did',
                    'defined',
                    'lid',
                    'multi',
                    'ftid',
                    $data->ScoreType . ' AS score',
                ],
                [
                    'join'       => [
                        "glider g"        => "flight.gid=g.gid",
                        "club c"          => "flight.cid=c.cid",
                        "pilot p"         => "flight.pid=p.pid",
                        'manufacturer gm' => 'g.mid = gm.mid',
                    ],
                    'where'      => implode(" AND ", $where),
                    'order'      => 'score DESC',
                    'limit'      => 10,
                    'parameters' => $data->parameters,
                ]
            );
            if ($flights) {
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
                        $flights->iterate_return(
                            function (flight $flight, $count) use (&$html, $data) {
                                $count++;
                                return node::create('tr', [],
                                    "<td>{$count}</td><td>{$flight->p_name}</td><td>{$flight->c_name}</td>" .
                                    ($data->class_table_alias == 'p' ? "<td>{$flight->g_name}</td>" : '') .
                                    $flight->to_print() .
                                    "<td>{$flight->coords}</td>"
                                );
                            }
                        )
                    )
                );
            }
        }
        return '<div id="table_wrapper">' . $html . '</div>';
    }


    function sortflights($a, $b): int {
        if ($a->score == $b->score) {
            return 0;
        }
        return ($a->score > $b->score) ? -1 : 1;
    }
}