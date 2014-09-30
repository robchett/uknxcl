<?phpnamespace module\tables\object;use classes\ajax;use html\node;use object\flight;class result_pilot extends result {    function make_table(league_table $data) {        $html = node::create('div.table_wrapper', [],            node::create('h3.heading', [], $data->Title) .            node::create('table.main.results.tablesorter', [],                node::create('thead tr', [],                    node::create('th', ['style' => 'width:42px'], 'Flight No') .                    node::create('th', ['style' => 'width:63px'], 'Date') .                    node::create('th', ['style' => 'width:90px'], 'Club') .                    node::create('th', ['style' => 'width:95px'], 'Glider') .                    node::create('th', ['style' => 'width:60px'], 'Score') .                    node::create('th', ['style' => 'width:298px'], 'Flight Waypoints')                ) .                $data->flights->iterate_return(                    function (flight $flight) {                        return node::create('tr', [],                            node::create('td', [], $flight->fid) .                            node::create('td', [], date('d/m/Y', $flight->date)) .                            node::create('td', [], $flight->c_name) .                            node::create('td', [], $flight->g_name) .                            $flight->to_print() .                            node::create('td', [], $flight->coords)                        );                    }                )            )        );        if($data->flights->count()) {            $script = '$("table.main").tablesorter( {            headers: {                0: {sorter: false},                1: {sorter: "uk_date"},                2: {sorter: false},                3: {sorter: false},                4: {sorter: "score"},                5: {sorter: false},            }        } ); ';            if (ajax) {                ajax::add_script($script);            } else {                \core::$inline_script[] = $script;            }        }        return $html;    }}