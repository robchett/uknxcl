<?php

use classes\db;

define('load_core', false);
include '../../index.php';
db::default_connection();
db::connect('localhost', 'nxcl2', 'root','','old');

set_time_limit(0);

$tables = array(
   /* 'flight' => array(
        'ID' => 'fid',
        'Pilot_No' => 'pid',
        'Club_No' => 'cid',
        'Glider_No' => 'gid',
        'Base_Score' => 'base_score',
        'Score' => 'score',
        'Multi' => 'multi',
        'Date' => 'date',
        'Season' => 'season',
        'DateAdded' => 'created',
        'Cords' => 'coords',
        'Launch' => 'lid',
        'Type' => 'ftid',
        'Defined' => 'defined',
        'Ridge' => 'ridge',
        'Winter' => 'winter',
        'Vis_Info' => 'vis_info',
        'Admin_Info' => 'admin_info',
        'Personal' => 'personal',
        'Delay' => 'delayed',
        'Comp' => 'comp_id',
        'dim' => 'did',
        'flighttime' => 'duration',
        'ODs' => 'od_score',
        'ODt' => 'od_time',
        'ORs' => 'or_score',
        'ORt' => 'or_time',
        'TRs' => 'tr_score',
        'TRt' => 'tr_time',
        'Speed' => 'speed',
    ),*/
   /* 'pilot' => array(
        'P_ID' => 'pid',
        'P_NAME' => 'name',
        'Gender' => 'gender',
        'BHPA' => 'bhpa_no',
        'Rating' => 'rating',
        'Email' => 'email',
    ),*/
    'glider' => array(
        'G_ID' => 'gid',
        'G_NAME' => 'name',
        'G_CLASS' => 'class',
        'Manufacturer' => array('get_manu', 'mid'),
        'Kingpost' => 'kingpost',
        'Single_Surface' => 'single_surface',
        'Hangies_League' => 'hangies',
    ),
    'wayoint' => array(
        'ID' => 'wid',
        'INFO' => 'title',
        'Lat' => 'lat',
        'Lon' => 'lon',
    ),
);

foreach ($tables as $table => $keys) {

    db::swap_connection('old');
    $res = db::query('SELECT * FROM ' . $table . 's');

    db::swap_connection('default');
    db::query('TRUNCATE ' . $table);

    // prepare_statement
    $sql_arr = array();
    $sql = 'INSERT INTO ' . $table . ' SET ';
    foreach ($keys as $old => $new) {
        if (is_array($new)) {
            $sql_arr[] = '`' . $new[1] . '`=:' . $old;
        } else {
            $sql_arr[] = '`' . $new . '`=:' . $old;
        }
    }
    $sql .= implode(', ', $sql_arr);
    $statement = db::$con->prepare($sql);


    while ($row = db::fetch($res)) {
        $params = array();
        foreach ($keys as $old => $new) {
            if (is_array($new)) {
                $params[$old] = $new[0] ($row->$old);
            } else {
                $params[$old] = $row->$old;
            }
        }

        $statement->execute($params);
    }
}
db::query('UPDATE flight SET lid = lid+1, ftid=ftid+1');

function get_manu($manu) {
    $res = db::result('SELECT mid FROM manufacturer WHERE title =:title', array('title' => $manu));
    if($res) {
        return $res->mid;
    }
}
