<?phpfunction makeTable(league_table $data) {    $html = '';    for ($i = 1; $i < 5; $i++) {        $where = $data->where;        $where .= ' AND ftid=' . $i;        $flights = flight::get_all(array('fid', 'flight.pid', 'flight.gid', $data->class_table_alias . '.' . $data->class_primary_key . ' AS ClassID', 'p.name AS p_name', 'c.name AS c_name', 'class, g.name AS g_name', 'coords', 'g.mid', 'kingpost', 'did', 'defined', 'lid', 'multi', 'ftid', $data->ScoreType . ' AS score'), array(                'join' => array(                    "glider g" => "flight.gid=g.gid",                    "club c" => "flight.cid=c.cid",                    "pilot p" => "flight.pid=p.pid"                ),                'where' => $where,                'order' => 'score DESC',                'limit' => 10            )        );        if ($flights) {            $html .= '<div class="table_wrapper"><h3>' . get::type($i) . ' - ' . $data->year . '</h3>                <table class="main results"><thead><tr>                <th style="width:20px">Pos</th>                <th style="width:100px">Name</th>                <th style="width:70px">Club</th>                <th style="width:100px">Glider</th>                <th style="width:58px">Score</th>                <th style="width:300px">Flight Waypoints</th></tr></thead>';            //$flights->iterate(function ($flight, $count) use (&$html) {            foreach ($flights as $flight) {                    $html .= '<tr>                        <td>' . $count . '</td>                        <td>' . $flight->p_name . '</td>                        <td><a>' . $flight->c_name . '</a></td>                        <td>' . $flight->g_name . '</td>' .                        $flight->to_print()->get() . '                        <td>' . $flight->coords . '</td>';            }                //});        }        $html .= '</table>';        $html .= '</div>';    }    return $html;}function sortflights($a, $b) {    if ($a->score == $b->score) {        return 0;    }    return ($a->score > $b->score) ? -1 : 1;}?>