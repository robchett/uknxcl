<?php
class result_records { use table;
    function make_table() {
        $html = '<div class="table_wrapper"><h3>Results</h3>';
        $html .= '<table class="results main">
    <thead>
    <tr>
        <th>Type</th>
        <th>Class</th>
        <th>Gender</th>
        <th>Name</th>
        <th>Score</th>
        <th>Date</th>
    </tr>
    </thead>';
        $html .= $this->get_flights(1, 'Open Distance');
        $html .= $this->get_flights(3, 'Goal', true);
        $html .= $this->get_flights(2, 'Out and return (Open)', 0);
        $html .= $this->get_flights(2, 'Out and return (Defined)', 1);
        $html .= $this->get_flights(4, 'Triangle (Open)', 0);
        $html .= $this->get_flights(4, 'Triangle (Defined)', 1);
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    function get_flights($type, $title, $defined = null) {
        $html = '<tr><td colspan="6" class="title">' . $title . '</td></tr>';
        $html .= $this->get_flight($type, 1, 'M');
        $html .= $this->get_flight($type, 5, 'M');
        $html .= $this->get_flight($type, 1, 'F');
        $html .= $this->get_flight($type, 5, 'F');
        if (isset($defined) && $defined) {
            $html .= $this->get_flight_defined($type, 1, 'M');
            $html .= $this->get_flight_defined($type, 5, 'M');
            $html .= $this->get_flight_defined($type, 1, 'F');
            $html .= $this->get_flight_defined($type, 5, 'F');
        }
        return $html;
    }

    protected function get_flight($ftid, $class, $gender) {
        $html = '';
        $flight = new flight();
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
        if ($flight->fid) {
            $html .= '<tr><td>Distance</td><td>' . $class . '</td><td>' . $gender . '</td><td>' . $flight->p_name . '</td><td>' . $flight->base_score . ' km</td><td>' . $flight->date . '</td></tr>';
        }
        return $html;
    }

    protected function get_flight_defined($ftid, $class, $gender) {
        $html = '';
        $flight = new flight();
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
        if ($flight->fid) {
            $html .= '<tr><td>Speed</td><td>' . $class . '</td><td>' . $gender . '</td><td>' . $flight->p_name . '</td><td>' . number_format($flight->speed,2) . ' km/h</td><td>' . $flight->date . '</td></tr>';
        }
        return $html;
    }
}