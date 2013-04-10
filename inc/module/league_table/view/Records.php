<?php
function makeTable() {
    $html = '<div class="table_wrapper"><h3>Results</h3>';
    $html .= '<table class="results main">
    <thead>
    <tr>
        <th>Type</th>
        <th>Gender</th>
        <th>Class</th>
        <th>Name</th>
        <th>Score</th>
        <th>Date</th>
    </tr>
    </thead>';
    $html .= get_flight(1, 'Open Distance');
    $html .= get_flight(3, 'Goal', true);
    $html .= get_flight(2, 'Out and return (Open)', 0);
    $html .= get_flight(2, 'Out and return (Defined)', 1);
    $html .= get_flight(4, 'Triangle (Open)', 0);
    $html .= get_flight(4, 'Triangle (Defined)', 1);
    $html .= '</table>';
    $html .= '</div>';

    return $html;
}

function get_flight($type, $title, $defined = null) {
    $html = '<tr><td colspan="6" class="title">' . $title . '</td></tr>';
    if ($t = db::fetch(db::query('SELECT p.name, base_score, date FROM flight LEFT JOIN pilot p ON flight.pid=p.pid JOIN glider g ON flight.gid=g.gid WHERE ftid=3 AND g.class=1 AND p.gender = 1 ORDER BY SCORE DESC LIMIT 1'))) {
        $html .= '<tr><td>Distance</td><td>1</td><td>M</td><td>' . $t->name . '</td><td>' . $t->base_score . '</td><td>' . $t->date . '</td></tr>';
    }
    if ($t = db::fetch(db::query('SELECT p.name, base_score, date FROM flight LEFT JOIN pilot p ON flight.pid=p.pid JOIN glider g ON flight.gid=g.gid WHERE ftid=3 AND g.class=5 AND p.gender = 1 ORDER BY SCORE DESC LIMIT 1'))) {
        $html .= '<tr><td>Distance</td><td>5</td><td>M</td><td>' . $t->name . '</td><td>' . $t->base_score . '</td><td>' . $t->date . '</td></tr>';
    }
    if ($t = db::fetch(db::query('SELECT p.name, base_score, date FROM flight LEFT JOIN pilot p ON flight.pid=p.pid JOIN glider g ON flight.gid=g.gid WHERE ftid=3 AND g.class=1 AND p.gender = 2 ORDER BY SCORE DESC LIMIT 1'))) {
        $html .= '<tr><td>Distance</td><td>1</td><td>F</td><td>' . $t->name . '</td><td>' . $t->base_score . '</td><td>' . $t->date . '</td></tr>';
    }
    if ($t = db::fetch(db::query('SELECT p.name, base_score, date FROM flight LEFT JOIN pilot p ON flight.pid=p.pid JOIN glider g ON flight.gid=g.gid WHERE ftid=3 AND g.class=2 AND p.gender = 2 ORDER BY SCORE DESC LIMIT 1'))) {
        $html .= '<tr><td>Distance</td><td>5</td><td>F</td><td>' . $t->name . '</td><td>' . $t->base_score . '</td><td>' . $t->date . '</td></tr>';
    }
    if (isset($defined) && $defined) {
        if($t = db::fetch(db::query('SELECT p.name, base_score, date, base_score, speed FROM flight LEFT JOIN pilot p ON flight.pid=p.pid JOIN glider g ON flight.gid=g.gid WHERE ftid=3 AND g.class=1 AND base_score>100 AND p.gender = 1 ORDER BY speed DESC LIMIT 1'))) {
            $html .= '<tr><td>speed</td><td>1</td><td>M</td><td>' . $t->name . '</td><td>' . $t->speed . '</td><td>' . $t->date . '</td></tr>';
        }
        if($t = db::fetch(db::query('SELECT p.name, base_score, date, base_score, speed FROM flight LEFT JOIN pilot p ON flight.pid=p.pid JOIN glider g ON flight.gid=g.gid WHERE ftid=3 AND g.class=5 AND base_score>100 AND p.gender = 1 ORDER BY speed DESC LIMIT 1'))) {
            $html .= '<tr><td>speed</td><td>5</td><td>M</td><td>' . $t->name . '</td><td>' . $t->speed . '</td><td>' . $t->date . '</td></tr>';
        }
        if($t = db::fetch(db::query('SELECT p.name, base_score, date, base_score, speed FROM flight LEFT JOIN pilot p ON flight.pid=p.pid JOIN glider g ON flight.gid=g.gid WHERE ftid=3 AND g.class=1 AND base_score>100 AND p.gender = 2 ORDER BY speed DESC LIMIT 1'))) {
            $html .= '<tr><td>speed</td><td>1</td><td>F</td><td>' . $t->name . '</td><td>' . $t->speed . '</td><td>' . $t->date . '</td></tr>';
        }
        if($t = db::fetch(db::query('SELECT p.name, base_score, date, base_score, speed FROM flight LEFT JOIN pilot p ON flight.pid=p.pid JOIN glider g ON flight.gid=g.gid WHERE ftid=3 AND g.class=5 AND base_score>100 AND p.gender = 2 ORDER BY speed DESC LIMIT 1'))) {
            $html .= '<tr><td>speed</td><td>5</td><td>F</td><td>' . $t->name . '</td><td>' . $t->speed . '</td><td>' . $t->date . '</td></tr>';
        }
    }
    return $html;
}