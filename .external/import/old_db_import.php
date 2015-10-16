<?php

use classes\db;

define('load_core', false);
include '../../index.php';
db::default_connection();
db::connect('localhost', 'nxcl_old', 'root', '', 'old');

set_time_limit(0);

$tables = array(
    'flight' => array(
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
    ),
    'pilot' => array(
        'P_ID' => 'pid',
        'P_NAME' => 'name',
        'Gender' => 'gender',
        'BHPA' => 'bhpa_no',
        'Rating' => 'rating',
        'Email' => 'email',
    ),
    'glider' => array(
        'G_ID' => 'gid',
        'G_NAME' => 'name',
        'G_CLASS' => 'class',
        'Manufacturer' => array('get_manu', 'mid'),
        'Kingpost' => 'kingpost',
        'Single_Surface' => 'single_surface',
        'Hangies_League' => 'hangies',
    ),
    'waypoint' => array(
        'ID' => 'wid',
        'INFO' => 'title',
        'Lat' => 'lat',
        'Lon' => 'lon',
    ),
);

foreach ($tables as $table => $keys) {

    echo '<h1>Importing: ' . $table . '</h1>';

    db::swap_connection('old');
    $res = db::query('SELECT * FROM ' . $table . 's');

    db::swap_connection('default');
    db::query('TRUNCATE ' . $table);

    // prepare_statement
    $sql_arr = [];
    $key_arr = [];
    foreach ($keys as $old => $new) {
        if (is_array($new)) {
            $sql_arr[] = '`' . $new[1] . '`';
        } else {
            $sql_arr[] = '`' . $new . '`';
        }
        $key_arr[] = ':' . $old . '_[id]';
    }
    $base_sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $sql_arr) . ') VALUES ';
    $key_set = '(' . implode(', ', $key_arr) . ')';
    $part_cnt = 0;
    $cnt = 0;

    $params = [];
    while ($row = db::fetch($res)) {

        $cnt++;
        $part_cnt++;
        $sql_sets[] = str_replace('[id]', $part_cnt, $key_set);

        foreach ($keys as $old => $new) {
            if (is_array($new)) {
                $params[$old . '_' . $part_cnt] = $new[0] ($row->$old);
            } else {
                $params[$old . '_' . $part_cnt] = $row->$old;
            }
        }


        if ($part_cnt == 500) {
            $statement = db::$con->prepare($base_sql . implode(',', $sql_sets));
            $statement->execute($params);
            echo '<p>' . $cnt . ' rows imported.</p>';
            $params = [];
            $sql_sets = [];
            $part_cnt = 0;
        }
    }

    if ($params) {
        $statement = db::$con->prepare($base_sql . implode(',', $sql_sets));
        $statement->execute($params);
        echo '<p>' . $cnt . ' rows imported.</p>';
        $params = [];
        $sql_sets = [];
        $part_cnt = 0;
    }

    echo '<p><span style="color:green">Completed</span></p>';
}


db::query('UPDATE flight SET lid = lid+1');
db::query('UPDATE flight SET ftid = ftid+1');
db::query('UPDATE flight SET `position` = fid');

function get_manu($manu) {
    $res = db::result('SELECT mid FROM manufacturer WHERE title =:title', array('title' => $manu));
    if ($res) {
        return $res->mid;
    }
}
