<?php
namespace module\cms\view;

use html\node;
use object\flight;
use object\glider;
use object\pilot;

class dashboard extends \core\module\cms\view\dashboard {

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
        $flights = flight::get_all(['fid', 'date', 'pilot.pid', 'pilot.name', 'glider.gid', 'glider.name', 'club.cid', 'club.title', 'admin_info', 'delayed'], ['join' => flight::$default_joins, 'limit' => 15, 'order' => 'fid DESC']);
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
            $flights->iterate_return(
                function (flight $flight) {
                    return node::create('tr', [],
                        node::create('td a', ['href' => '/cms/module/2/' . $flight->fid, 'title' => 'Flight: ' . $flight->fid], $flight->fid) .
                        node::create('td a', ['href' => '/cms/module/2/' . $flight->fid, 'title' => 'Flight: ' . $flight->fid], $flight->date) .
                        node::create('td a', ['href' => '/cms/module/3/' . $flight->pilot_pid, 'title' => 'Pilot: ' . $flight->pilot_name], $flight->pilot_name) .
                        node::create('td a', ['href' => '/cms/module/4/' . $flight->glider_gid, 'title' => 'Glider: ' . $flight->glider_name], $flight->glider_name) .
                        node::create('td a', ['href' => '/cms/module/12/' . $flight->club_cid, 'title' => 'Club: ' . $flight->club_name], $flight->club_name) .
                        node::create('td', [], $flight->admin_info) .
                        node::create('td', [], $flight->delayed ? 'Yes' : 'No')
                    );
                }
            )
        );
        return $table;
    }

    public function get_latest_pilots() {
        $pilots = pilot::get_all(['pid', 'name', 'bhpa_no', 'email'], ['limit' => 5, 'order' => 'pid DESC']);
        $table = node::create('table#latest_pilots.module', [],
            node::create('thead', [],
                node::create('th', [], 'ID') .
                node::create('th', [], 'Pilot') .
                node::create('th', [], 'BHPA Number') .
                node::create('th', [], 'Email')
            ) .
            $pilots->iterate_return(
                function (pilot $pilot) {
                    return node::create('tr', [],
                        node::create('td a', ['href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name], $pilot->pid) .
                        node::create('td a', ['href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name], $pilot->name) .
                        node::create('td a', ['href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name], $pilot->bhpa_no) .
                        node::create('td a', ['href' => '/cms/module/3/' . $pilot->pid, 'title' => 'Pilot: ' . $pilot->name], $pilot->email)
                    );
                }
            )
        );
        return $table;
    }

    public function get_latest_gliders() {
        $gliders = glider::get_all(['gid', 'name', 'manufacturer.title'], ['join' => ['manufacturer' => 'manufacturer.mid = glider.mid'], 'limit' => 5, 'order' => 'gid DESC']);
        $table = node::create('table#latest_pilots.module', [],
            node::create('thead', [],
                node::create('th', [], 'ID') .
                node::create('th', [], 'Glider') .
                node::create('th', [], 'Manufacturer')
            ) .
            $gliders->iterate_return(
                function (glider $glider) {
                    return node::create('tr', [],
                        node::create('td a', ['href' => '/cms/module/4/' . $glider->gid, 'title' => 'Glider: ' . $glider->name], $glider->gid) .
                        node::create('td a', ['href' => '/cms/module/4/' . $glider->gid, 'title' => 'Glider: ' . $glider->name], $glider->name) .
                        node::create('td a', ['href' => '/cms/module/5/' . $glider->gid, 'title' => 'Manufacturer: ' . $glider->manufacturer_title], $glider->manufacturer_title)

                    );
                }
            )
        );
        return $table;
    }
}
