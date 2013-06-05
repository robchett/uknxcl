<?php
class dashboard_view extends cms_view {

    public function get_view() {
        $html = html_node::create('div')->nest(array(
                html_node::create('h2', 'Welcome to the dashboard'),
                 html_node::create('div#summaries.cf')->nest(
                    array(
                        html_node::create('div')->nest(
                            array(
                                html_node::create('h4',html_node::inline('a','Latest Flights', array('href'=>'/cms/module/2', 'title'=>'View all flights'))),
                                $this->get_latest_flights()
                            )
                        ),
                        html_node::create('div')->nest(
                            array(
                                html_node::create('h4',html_node::inline('a','Latest Pilots', array('href'=>'/cms/module/3', 'title'=>'View all pilots'))),
                                $this->get_latest_pilots()
                            )
                        ),
                         html_node::create('div')->nest(
                             array(
                                 html_node::create('h4',html_node::inline('a','Latest Gliders', array('href'=>'/cms/module/12', 'title'=>'View all gliders'))),
                                 $this->get_latest_gliders()
                             )
                         )
                    )
                )
            )
        );
        return $html;
    }

    public function get_latest_flights() {
        $flights = flight::get_all(array('fid', 'date', 'pilot.pid', 'pilot.name', 'glider.gid', 'glider.name', 'club.cid', 'club.name', 'admin_info'), array('join' => flight::$default_joins, 'limit' => 15, 'order' => 'fid DESC'));
        $table = html_node::create('table#latest_flights');
        $table->nest(
            html_node::create('thead')->nest(
                array(
                    html_node::create('th', 'ID'),
                    html_node::create('th', 'Date Added'),
                    html_node::create('th', 'Pilot'),
                    html_node::create('th', 'Glider'),
                    html_node::create('th', 'Club'),
                    html_node::create('th', 'Admin Notes'),
                )
            )
        );
        $flights->iterate(function ($flight) use ($table) {
                $table->nest(
                    html_node::create('tr')->nest(
                        array(
                            html_node::create('td', html_node::inline('a', $flight->fid, array('href' => '/cms/module/2/' . $flight->fid, 'title' => 'Flight: ' . $flight->fid))),
                            html_node::create('td', html_node::inline('a', $flight->date, array('href' => '/cms/module/2/' . $flight->fid, 'title' => 'Flight: ' . $flight->fid))),
                            html_node::create('td', html_node::inline('a', $flight->pilot_name, array('href' => '/cms/module/3/' . $flight->pilot_pid, 'title' => 'Pilot: ' . $flight->pilot_name))),
                            html_node::create('td', html_node::inline('a', $flight->glider_name, array('href' => '/cms/module/4/' . $flight->glider_gid, 'title' => 'Glider: ' . $flight->glider_name))),
                            html_node::create('td', html_node::inline('a', $flight->club_name, array('href' => '/cms/module/12/' . $flight->club_cid, 'title' => 'Club: ' . $flight->club_name))),
                            html_node::create('td',$flight->admin_info),
                        )
                    )
                );
            }
        );
        return $table;
    }

    public function get_latest_pilots() {
        $pilots = pilot::get_all(array('pid', 'name', 'bhpa_no', 'email'), array( 'limit' => 5, 'order' => 'pid DESC'));
        $table = html_node::create('table#latest_pilots');
        $table->nest(
            html_node::create('thead')->nest(
                array(
                    html_node::create('th', 'ID'),
                    html_node::create('th', 'Pilot'),
                    html_node::create('th', 'BHPA Number'),
                    html_node::create('th', 'Email'),
                )
            )
        );
        $pilots->iterate(function ($pilot) use ($table) {
                $table->nest(
                    html_node::create('tr')->nest(
                        array(
                            html_node::create('td', html_node::inline('a', $pilot->pid, array('href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name))),
                            html_node::create('td', html_node::inline('a', $pilot->name, array('href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name))),
                            html_node::create('td', html_node::inline('a', $pilot->bhpa_no, array('href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name))),
                            html_node::create('td', html_node::inline('a', $pilot->email, array('href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name))),

                        )
                    )
                );
            }
        );
        return $table;
    }

    public function get_latest_gliders() {
        $gliders = glider::get_all(array('gid', 'name', 'manufacturer.title'), array('join'=> array('manufacturer' => 'manufacturer.mid = glider.mid'), 'limit' => 5, 'order' => 'gid DESC'));
        $table = html_node::create('table#latest_pilots');
        $table->nest(
            html_node::create('thead')->nest(
                array(
                    html_node::create('th', 'ID'),
                    html_node::create('th', 'Glider'),
                    html_node::create('th', 'Manufacturer'),
                )
            )
        );
        $gliders->iterate(function ($glider) use ($table) {
                $table->nest(
                    html_node::create('tr')->nest(
                        array(
                            html_node::create('td', html_node::inline('a', $glider->gid, array('href' => '/cms/module/4/' . $glider->gid, 'title' => 'Glider: ' . $glider->name))),
                            html_node::create('td', html_node::inline('a', $glider->name, array('href' => '/cms/module/4/' . $glider->gid, 'title' => 'Glider: ' . $glider->name))),
                            html_node::create('td', html_node::inline('a', $glider->manufacturer_title, array('href' => '/cms/module/5/' . $glider->gid, 'title' => 'Manufacturer: ' . $glider->manufacturer_title))),

                        )
                    )
                );
            }
        );
        return $table;
    }
}
