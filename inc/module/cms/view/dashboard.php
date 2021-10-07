<?php

namespace module\cms\view;

use classes\tableOptions;
use model\flight;
use model\glider;
use model\pilot;

class dashboard extends cms_view {

    public function get_view(): string {
        $flights = $this->get_latest_flights();
        $pilots = $this->get_latest_pilots();
        $gliders = $this->get_latest_gliders();
        return "
<div>
    <h2 class='page-header container-fluid'>Welcome to the dashboard</h2>
    <div id='summaries'>
        <div>
            <div class='col-md-6'>
                <h4><a href='/cms/module/2' title='View all flights'>Latest Flights</a></h4>
                $flights
            </div> 
            <div class='col-md-6'>
                <div>
                    <h4><a href='/cms/module/3' title='View all pilots'>Latest Pilots</a></h4>
                    $pilots
                </div>
                <div>
                    <h4><a href='/cms/module/12' title='View all gliders'>Latest Gliders</a></h4>
                    $gliders
                </div>
            </div>
        </div>
    </div>
</div>";
    }

    public function get_latest_flights(): string {
        $flights = flight::get_all(new tableOptions(
            limit: '14',
            order: 'fid DESC',
        ));
        return "
<table id='latest_flights' class='module table table-striped'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date Added</th>
            <th>Pilot</th>
            <th>Glider</th>
            <th>Club</th>
            <th>Admin Notes</th>
            <th>Delayed</th>
        </tr>
    </thead>
    {$flights->reduce(fn(string $_, flight $flight): string => $_ . "
    <tr>
        <td><a href='/cms/module/2/{$flight->fid}' title='Flight: {$flight->fid}'>{$flight->fid}</a></td>
        <td><a href='/cms/module/2/{$flight->fid}' title='Flight: {$flight->fid}'>{$flight->get_date_string('d/m/Y')}</a></td>
        <td><a href='/cms/module/3/{$flight->pilot->pid}' title='Pilot: {$flight->pilot->name}'>{$flight->pilot->name}</a></td>
        <td><a href='/cms/module/4/{$flight->glider->gid}' title='Glider: {$flight->glider->name}'>{$flight->glider->name}</a></td>
        <td><a href='/cms/module/12/{$flight->club->cid}' title='Club: {$flight->club->title}'>{$flight->club->title}</a></td>
        <td class='col-md-5'>{$flight->admin_info}</td>
        <td>{$flight->get_delayed_string()}</td>
    </tr>", ''
    )}
</table>";
    }

    public function get_latest_pilots(): string {
        $pilots = pilot::get_all(new tableOptions(limit: '5', order: 'pid DESC'));
        return "
<table id='latest_pilots' class='module table table-striped'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Pilot</th>
            <th>BHPA Number</th>
            <th>Email</th>
        </tr>
    </thead>
    {$pilots->reduce(fn(string $acc, pilot $pilot): string => $acc . "
    <tr>
        <td><a href='/cms/module/3/{$pilot->pid}' title='Pilot: {$pilot->name}'>{$pilot->pid}</a></td>
        <td><a href='/cms/module/3/{$pilot->pid}' title='Pilot: {$pilot->name}'>{$pilot->name}</a></td>
        <td><a href='/cms/module/3/{$pilot->pid}' title='Pilot: {$pilot->name}'>{$pilot->bhpa_no}</a></td>
        <td><a href='/cms/module/3/{$pilot->pid}' title='Pilot: {$pilot->name}'>{$pilot->email}</a></td>
    </tr>", ''
    )}
</table>";
    }

    public function get_latest_gliders(): string {
        $gliders = glider::get_all(new tableOptions(join: ['manufacturer' => 'manufacturer.mid = glider.mid'], limit: '5', order: 'gid DESC'));
        return "
<table id='latest_pilots' class='module table table-striped'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Glider</th>
            <th>Manufacturer</th>
        </tr>
    </thead>
    {$gliders->reduce(fn(string $_, glider $glider): string => $_ . "
    <tr>
        <td><a href='/cms/module/4/{$glider->gid}' title='Glider: {$glider->name}'>{$glider->gid}</a></td>
        <td><a href='/cms/module/4/{$glider->gid}' title='Glider: {$glider->name}'>{$glider->name}</a></td>
        <td><a href='/cms/module/4/{$glider->gid}' title='Glider: {$glider->name}'>{$glider->manufacturer->title}</a></td>
    </tr>", ''
    )}
</table>";
    }
}
