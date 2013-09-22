<?php
namespace cms;

use html\node;

class dashboard_view extends cms_view {

    public function get_view() {
        $html = node::create('div', [],
            node::create('h2', [], 'Welcome to the dashboard') .
            node::create('div#summaries.cf', [],
                node::create('div', [],
                    node::create('h4 a', ['href' => '/cms/module/2', 'title' => 'View all flights'], 'Latest Flights') .
                    $this->get_latest_flights()
                ) .
                node::create('div', [],
                    node::create('h4 a', ['href' => '/cms/module/3', 'title' => 'View all pilots'], 'Latest Pilots') .
                    $this->get_latest_pilots()
                ) .
                node::create('div', [],
                    node::create('h4 a', ['href' => '/cms/module/12', 'title' => 'View all gliders'], 'Latest Gliders') .
                    $this->get_latest_gliders()
                )
            )
        );
        return $html;
    }

    public function get_latest_flights() {
        $table = node::create('table#latest_flights.module', [],
            node::create('thead', [],
                node::create('th', [], 'ID') .
                node::create('th', [], 'Date Added') .
                node::create('th', [], 'Pilot') .
                node::create('th', [], 'Glider') .
                node::create('th', [], 'Club') .
                node::create('th', [], 'Admin Notes') .
                node::create('th', [], 'Delayed')
            ) .
            node::nest_function(
                function () {
                    $flights = \flight::get_all(['fid', 'date', 'pilot.pid', 'pilot.name', 'glider.gid', 'glider.name', 'club.cid', 'club.title', 'admin_info', 'delayed'], ['join' => \flight::$default_joins, 'limit' => 15, 'order' => 'fid DESC']);
                    $body = '';
                    $flights->iterate(
                        function (\flight $flight) use (&$body) {
                            $body .= node::create('tr', [],
                                node::create('td a', ['href' => '/cms/module/2/' . $flight->fid, 'title' => 'Flight: ' . $flight->fid], $flight->fid) .
                                node::create('td a', ['href' => '/cms/module/2/' . $flight->fid, 'title' => 'Flight: ' . $flight->fid], $flight->date) .
                                node::create('td a', ['href' => '/cms/module/3/' . $flight->pilot_pid, 'title' => 'Pilot: ' . $flight->pilot_name], $flight->pilot_name) .
                                node::create('td a', ['href' => '/cms/module/4/' . $flight->glider_gid, 'title' => 'Glider: ' . $flight->glider_name], $flight->glider_name) .
                                node::create('td a', ['href' => '/cms/module/12/' . $flight->club_cid, 'title' => 'Club: ' . $flight->club_name], $flight->club_name) .
                                node::create('td', [], $flight->admin_info) .
                                node::create('td', [], $flight->delayed ? 'Yes' : 'No')
                            );
                        }
                    );
                    return $body;
                }
            )
        );
        return $table;
    }

    public function get_latest_pilots() {

        $table = node::create('table#latest_pilots.module', [],
            node::create('thead', [],
                node::create('th', [], 'ID') .
                node::create('th', [], 'Pilot') .
                node::create('th', [], 'BHPA Number') .
                node::create('th', [], 'Email')
            ) .
            node::nest_function(
                function () {
                    $body = '';
                    $pilots = \pilot::get_all(['pid', 'name', 'bhpa_no', 'email'], ['limit' => 5, 'order' => 'pid DESC']);
                    $pilots->iterate(
                        function (\pilot $pilot) use (&$body) {
                            $body .= node::create('tr', [],
                                node::create('td a', ['href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name], $pilot->pid) .
                                node::create('td a', ['href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name], $pilot->name) .
                                node::create('td a', ['href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name], $pilot->bhpa_no) .
                                node::create('td a', ['href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name], $pilot->email)
                            );
                        }
                    );
                    return $body;
                }
            )
        );
        return $table;
    }

    public function get_latest_gliders() {

        $table = node::create('table#latest_pilots.module', [],
            node::create('thead', [],
                node::create('th', [], 'ID') .
                node::create('th', [], 'Glider') .
                node::create('th', [], 'Manufacturer')
            ) .
            node::nest_function(
                function () {
                    $body = '';
                    $gliders = \glider::get_all(['gid', 'name', 'manufacturer.title'], ['join' => ['manufacturer' => 'manufacturer.mid = glider.mid'], 'limit' => 5, 'order' => 'gid DESC']);
                    $gliders->iterate(
                        function (\glider $glider) use (&$body) {
                            $body .= node::create('tr', [],
                                node::create('td a', ['href' => '/cms/module/4/' . $glider->gid, 'title' => 'Glider: ' . $glider->name], $glider->gid) .
                                node::create('td a', ['href' => '/cms/module/4/' . $glider->gid, 'title' => 'Glider: ' . $glider->name], $glider->name) .
                                node::create('td a', ['href' => '/cms/module/5/' . $glider->gid, 'title' => 'Manufacturer: ' . $glider->manufacturer_title], $glider->manufacturer_title)

                            );
                        }
                    );
                    return $body;
                }
            )
        );
        return $table;
    }
}
