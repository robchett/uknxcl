<?php

use classes\error_handler;
use classes\table;
use form\schema;
use model\club;
use model\dimension;
use model\flight;
use model\flight_type;
use model\gender;
use model\glider;
use model\launch_type;
use model\manufacturer;
use model\new_flight_notification;
use model\pilot;
use model\pilot_rating;
use module\cms\model\_cms_user;
use module\comps\model\comp;
use module\comps\model\comp_group;
use module\comps\model\comp_type;
use module\news\model\article;
use module\pages\model\page;
use module\planner\model\declaration;
use module\planner\model\waypoint;
use module\planner\model\waypoint_group;

try {
    define('root', __DIR__);
    define('load_core', isset($_SERVER['REQUEST_URI']));

    spl_autoload_register(function (string $class): bool {
        $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $path = root . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . $class_path . '.php';

        if (file_exists($path)) {
            require_once($path);
            return true;
        }
        return false;
    });
    require __DIR__ . '/vendor/autoload.php';

    set_error_handler([\classes\error_handler::class, 'handle_error']);
    register_shutdown_function([\classes\error_handler::class, 'fatal_handler']);

    schema::setSchema([
        'page' => new schema(primary_key: 'pid', namespace: 'pages', table_name: 'page', object: page::class, fields: [
            'pid' => new form\field_int(fid: 1, field_name: 'pid', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 2, field_name: 'title', label: 'Title', required: true,),
            'nav_title' => new form\field_string(fid: 3, field_name: 'nav_title', label: 'Navigation Title',),
            'nav' => new form\field_string(fid: 4, field_name: 'nav', label: 'Show on Navigation?',),
            'body' => new form\field_textarea(fid: 5, field_name: 'body', label: 'Body Text', list: false,),
            'module_name' => new form\field_string(fid: 6, field_name: 'module_name', label: 'Module',),
            'fn' => new form\field_string(fid: 182, field_name: 'fn', label: 'Fn', filter: true,),
            'direct_link' => new form\field_string(fid: 183, field_name: 'direct_link', label: 'Direct Link', filter: true,),
            'info' => new form\field_string(fid: 205, field_name: 'info', label: 'Info', filter: true,),
            'icon' => new form\field_string(fid: 206, field_name: 'icon', label: 'Icon', filter: true,),
        ],),
        'comp_group' => new schema(primary_key: 'cgid', namespace: 'comps', table_name: 'comp_group', object: comp_group::class, fields: [
            'cgid' => new form\field_int(fid: 90, field_name: 'cgid', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 92, field_name: 'title', label: 'Title',),
        ],),
        'waypoint_group' => new schema(primary_key: 'wgid', namespace: 'planner', table_name: 'waypoint_group', object: waypoint_group::class, fields: [
            'wgid' => new form\field_int(fid: 130, field_name: 'wgid', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 132, field_name: 'title', label: 'Title',),
        ],),
        'waypoint' => new schema(primary_key: 'wid', namespace: 'planner', table_name: 'waypoint', object: waypoint::class, fields: [
            'wid' => new form\field_int(fid: 133, field_name: 'wid', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 135, field_name: 'title', label: 'Title', filter: true,),
            'lon' => new form\field_float(fid: 137, field_name: 'lon', label: 'Longitude',),
            'lat' => new form\field_float(fid: 136, field_name: 'lat', label: 'Latitude',),
            'wgid' => new form\field_link(fid: 138, field_name: 'wgid', label: 'Group', filter: true, link_module: waypoint_group::class, link_field: 'name'),
        ],),
        'comp_type' => new schema(primary_key: 'ctid', namespace: 'comps', table_name: 'comp_type', object: comp_type::class, fields: [
            'ctid' => new form\field_int(fid: 139, field_name: 'ctid', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 141, field_name: 'title', label: 'Title',),
        ],),
        '_cms_user' => new schema(primary_key: 'uid', namespace: 'cms', table_name: '_cms_user', object: _cms_user::class, fields: [
            'uid' => new form\field_int(fid: 199, field_name: 'uid', label: 'Uid', list: false, required: true,),
            'last_login_ip' => new form\field_string(fid: 204, field_name: 'last_login_ip', label: 'Last_login_ip', list: false, required: true,),
            'last_login' => new form\field_date(fid: 203, field_name: 'last_login', label: 'Last_login', list: false, required: true,),
            'password' => new form\field_password(fid: 202, field_name: 'password', label: 'Password', filter: true, required: true,),
            'title' => new form\field_string(fid: 201, field_name: 'title', label: 'Title', filter: true, required: true,),
        ],),
        'comp' => new schema(primary_key: 'cid', namespace: 'comps', table_name: 'comp', object: comp::class, fields: [
            'cid' => new form\field_int(fid: 80, field_name: 'cid', label: 'ID', list: false,),
            'type' => new form\field_string(fid: 82, field_name: 'type', label: 'Event', filter: true, required: true,),
            'round' => new form\field_int(fid: 83, field_name: 'round', label: 'Round', filter: true, required: true,),
            'task' => new form\field_int(fid: 84, field_name: 'task', label: 'Task', filter: true, required: true,),
            'date' => new form\field_date(fid: 85, field_name: 'date', label: 'Date', filter: true, required: true,),
            'title' => new form\field_string(fid: 86, field_name: 'title', label: 'Task Name', list: false, required: true,),
            'coords' => new form\field_textarea(fid: 87, field_name: 'coords', label: 'Task Coordinates', list: false, required: true),
            'cgid' => new form\field_link(fid: 89, field_name: 'cgid', label: 'Class', filter: true, required: true, link_module: comp_group::class, link_field: 'title',),
            'reverse_pilot_name' => new form\field_boolean(fid: 94, field_name: 'reverse_pilot_name', label: 'Reverse Pilot Name?',),
            'generate_kml' => new form\field_button(fid: 93, field_name: 'generate_kml', label: 'Generate KML',),
            'file' => new form\field_file(fid: 95, field_name: 'file', label: 'File',),
        ],),
        'pilot_rating' => new schema(primary_key: 'prid', namespace: '', table_name: 'pilot_rating', object: pilot_rating::class, fields: [
            'prid' => new form\field_int(fid: 74, field_name: 'prid', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 76, field_name: 'title', label: 'Title',),
        ],),
        'flight' => new schema(primary_key: 'fid', namespace: '', table_name: 'flight', object: flight::class, fields: [
            'fid' => new form\field_int(fid: 7, field_name: 'fid', label: 'ID',),
            'pid' => new form\field_link(fid: 58, field_name: 'pid', label: 'Pilot', filter: true, required: true, link_module: pilot::class, link_field: 'name',),
            'cid' => new form\field_link(fid: 60, field_name: 'cid', label: 'Club', filter: true, required: true, link_module: club::class, link_field: 'title',),
            'gid' => new form\field_link(fid: 59, field_name: 'gid', label: 'Glider', filter: true, required: true, link_module: glider::class, link_field: 'name',),
            'date' => new form\field_date(fid: 9, field_name: 'date', label: 'Date', required: true,),
            'did' => new form\field_link(fid: 18, field_name: 'did', label: 'Dimensions', filter: true, link_module: dimension::class, link_field: 'title',),
            'winter' => new form\field_boolean(fid: 16, field_name: 'winter', label: 'Was Winter',),
            'admin_info' => new form\field_textarea(fid: 15, field_name: 'admin_info', label: 'Admin Info',),
            'vis_info' => new form\field_textarea(fid: 14, field_name: 'vis_info', label: 'Visible Info',),
            'ftid' => new form\field_link(fid: 12, field_name: 'ftid', label: 'Type', filter: true, required: true, link_module: flight_type::class, link_field: 'title',),
            'lid' => new form\field_link(fid: 11, field_name: 'lid', label: 'Launch', filter: true, required: true, link_module: launch_type::class, link_field: 'title',),
            'multi' => new form\field_float(fid: 13, field_name: 'multi', label: 'Multiplier', required: true,),
            'score' => new form\field_float(fid: 10, field_name: 'score', label: 'Score', required: true,),
            'base_score' => new form\field_float(fid: 100, field_name: 'base_score', label: 'Base Score', list: false, required: true,),
            'coords' => new form\field_string(fid: 8, field_name: 'coords', label: 'Coordinates', required: true,),
            'personal' => new form\field_boolean(fid: 117, field_name: 'personal', label: 'Personal', list: false, filter: true,),
            'ridge' => new form\field_boolean(fid: 118, field_name: 'ridge', label: 'Ridge Lift', list: false, filter: true,),
            'delayed' => new form\field_boolean(fid: 17, field_name: 'delayed', label: 'Is Delayed?', filter: true,),
            'defined' => new form\field_boolean(fid: 119, field_name: 'defined', label: 'Defined', filter: true,),
            'season' => new form\field_int(fid: 120, field_name: 'season', label: 'Season', filter: true,),
            'file' => new form\field_file(fid: 96, field_name: 'file', label: 'IGC',),
            'generate_files' => new form\field_button(fid: 97, field_name: 'generate_files', label: 'Generate Files', list: false,),
            'duration' => new form\field_int(fid: 105, field_name: 'duration', label: 'Duration', list: false,),
            'od_score' => new form\field_float(fid: 106, field_name: 'od_score', label: 'Open Distance Score', list: false,),
            'od_time' => new form\field_int(fid: 107, field_name: 'od_time', label: 'Open Distance Duration', list: false,),
            'od_coordinates' => new form\field_string(fid: 114, field_name: 'od_coordinates', label: 'Open Distance Coordinates', list: false,),
            'or_time' => new form\field_int(fid: 108, field_name: 'or_time', label: 'Out & Return Duration', list: false,),
            'or_score' => new form\field_float(fid: 111, field_name: 'or_score', label: 'Out & Return Score', list: false,),
            'or_coordinates' => new form\field_string(fid: 115, field_name: 'or_coordinates', label: 'Out & Return Coordinates', list: false,),
            'tr_time' => new form\field_int(fid: 109, field_name: 'tr_time', label: 'Triangle Duration', list: false,),
            'tr_score' => new form\field_float(fid: 112, field_name: 'tr_score', label: 'Trangle Score', list: false,),
            'tr_coordinates' => new form\field_string(fid: 116, field_name: 'tr_coordinates', label: 'Triangle Coordinates', list: false,),
            'go_time' => new form\field_int(fid: 109, field_name: 'go_time', label: 'Goal Duration', list: false,),
            'go_score' => new form\field_float(fid: 112, field_name: 'go_score', label: 'Goal Score', list: false,),
            //'go_coordinates' => new form\field_string(fid: 116, field_name: 'go_coordinates', label: 'Goal Coordinates', list: false,),
            'os_codes' => new form\field_string(fid: 144, field_name: 'os_codes', label: 'OS Codes', list: false,),
        ],),
        'pilot' => new schema(primary_key: 'pid', namespace: '', table_name: 'pilot', object: pilot::class, fields: [
            'pid' => new form\field_int(fid: 19, field_name: 'pid', label: 'ID',),
            'name' => new form\field_string(fid: 20, field_name: 'name', label: 'Name',),
            'bhpa_no' => new form\field_int(fid: 21, field_name: 'bhpa_no', label: 'BHPA Number',),
            'prid' => new form\field_link(fid: 22, field_name: 'prid', label: 'Rating', link_module: pilot_rating::class, link_field: 'name',),
            'gid' => new form\field_link(fid: 23, field_name: 'gid', label: 'Gender', link_module: gender::class, link_field: 'name',),
            'email' => new form\field_email(fid: 24, field_name: 'email', label: 'Email',),
        ],),
        'glider' => new schema(primary_key: 'gid', namespace: '', table_name: 'glider', object: glider::class, fields: [
            'gid' => new form\field_int(fid: 26, field_name: 'gid', label: 'ID',),
            'name' => new form\field_string(fid: 27, field_name: 'name', label: 'Name',),
            'mid' => new form\field_link(fid: 28, field_name: 'mid', label: 'Manufacturer', link_module: manufacturer::class, link_field: 'name',),
            'class' => new form\field_int(fid: 29, field_name: 'class', label: 'Class',),
            'kingpost' => new form\field_boolean(fid: 30, field_name: 'kingpost', label: 'Kingposted?',),
            'single_surface' => new form\field_boolean(fid: 31, field_name: 'single_surface', label: 'Single Surface?',),
        ],),
        'manufacturer' => new schema(primary_key: 'mid', namespace: '', table_name: 'manufacturer', object: manufacturer::class, fields: [
            'mid' => new form\field_int(fid: 43, field_name: 'mid', label: 'ID',),
            'title' => new form\field_string(fid: 43, field_name: 'title', label: 'Title',),
        ],),
        'article' => new schema(primary_key: 'aid', namespace: 'news', table_name: 'article', object: article::class, fields: [
            'aid' => new form\field_int(fid: 32, field_name: 'aid', label: 'ID', list: false,),
            'poster' => new form\field_string(fid: 34, field_name: 'poster', label: 'Poster',),
            'title' => new form\field_string(fid: 39, field_name: 'title', label: 'Title',),
            'date' => new form\field_date(fid: 40, field_name: 'date', label: 'Date',),
            'snippet' => new form\field_textarea(fid: 41, field_name: 'snippet', label: 'Snippet',),
            'post' => new form\field_textarea(fid: 42, field_name: 'post', label: 'Content',),
        ],),
        'launch_type' => new schema(primary_key: 'lid', namespace: '', table_name: 'launch_type', object: launch_type::class, fields: [
            'lid' => new form\field_int(fid: 44, field_name: 'lid', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 46, field_name: 'title', label: 'Title',),
            'fn' => new form\field_string(fid: 98, field_name: 'fn', label: 'Short Name',),
        ],),
        'flight_type' => new schema(primary_key: 'ftid', namespace: '', table_name: 'flight_type', object: flight_type::class, fields: [
            'ftid' => new form\field_int(fid: 47, field_name: 'ftid', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 49, field_name: 'title', label: 'Title',),
            'fn' => new form\field_string(fid: 99, field_name: 'fn', label: 'Short Name',),
            'multi' => new form\field_float(fid: 50, field_name: 'multi', label: 'Multiplier',),
            'multi_defined' => new form\field_float(fid: 52, field_name: 'multi_defined', label: 'Defined Multiplier',),
            'multi_old' => new form\field_float(fid: 51, field_name: 'multi_old', label: 'Multiplier (Pre 2001)',),
            'multi_defined_old' => new form\field_float(fid: 53, field_name: 'multi_defined_old', label: 'Defined Multiplier (pre 2001)',),
        ],),
        'dimension' => new schema(primary_key: 'did', namespace: '', table_name: 'dimension', object: dimension::class, fields: [
            'did' => new form\field_int(fid: 54, field_name: 'did', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 56, field_name: 'title', label: 'Title',),
            'dimensions' => new form\field_int(fid: 57, field_name: 'dimensions', label: 'Dimension',),
        ],),
        'club' => new schema(primary_key: 'cid', namespace: '', table_name: 'club', object: club::class, fields: [
            'cid' => new form\field_int(fid: 61, field_name: 'cid', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 63, field_name: 'title', label: 'Title',),
        ],),
        'declaration' => new schema(primary_key: 'did', namespace: 'planner', table_name: 'declaration', object: declaration::class, fields: [
            'did' => new form\field_int(fid: 64, field_name: 'did', label: 'ID', list: false,),
            'coordinates' => new form\field_string(fid: 66, field_name: 'coordinates', label: 'Coordinates', required: true,),
            'date' => new form\field_date(fid: 67, field_name: 'date', label: 'Date', required: true,),
            'pid' => new form\field_link(fid: 68, field_name: 'pid', label: 'Pilot', required: true, link_module: pilot::class, link_field: 'name',),
            'ftid' => new form\field_link(fid: 69, field_name: 'ftid', label: 'Flight Type', required: true, link_module: flight_type::class, link_field: 'name',),
        ],),
        'gender' => new schema(primary_key: 'gid', namespace: '', table_name: 'gender', object: gender::class, fields: [
            'gid' => new form\field_int(fid: 70, field_name: 'gid', label: 'ID', list: false,),
            'title' => new form\field_string(fid: 72, field_name: 'title', label: 'Title',),
            'short' => new form\field_string(fid: 73, field_name: 'short', label: 'Abreviation',),
        ],),
        'new_flight_notification' => new schema(primary_key: 'nfid', namespace: '', table_name: 'new_flight_notification', object: new_flight_notification::class, fields: [
            'nfid' => new form\field_int(fid: 209, field_name: 'nfid', label: 'ID', filter: true,),
            'email' => new form\field_string(fid: 208, field_name: 'email', label: 'Email', filter: true,),
        ],),
    ], [
        'Pages' => [
            'Pages' => 'page'
        ],
        'Content' => [
            'Articles' => 'article'
        ],
        'Flights' => [
            'Flights' => 'flight',
            'Flight notifications' => 'new_flight_notification'
        ],
        'Links' => [
            'Pilots' => 'pilot',
            'Genders' => 'gender',
            'Declarations' => 'declaration',
            'Clubs' => 'club',
            'Dimensions' => 'dimension',
            'Flight types' => 'flight_type',
            'Launch types' => 'launch_type',
            'Manufacturers' => 'manufacturer',
            'Gliders' => 'glider',
            'Pilot ratings' => 'pilot_rating'
        ],
        'Waypoints' => [
            'Waypoints' => 'waypoint',
            'Waypoint groups' => 'waypoint_group'
        ],
        'Competitions' => [
            'Competions' => 'comp',
            'Competion groups' => 'comp_group',
            'Competion types' => 'comp_type'
        ],
    ]);

    if (load_core) {
        define('ajax', isset($_REQUEST['module']));
        define('host', (string) ($_SERVER['HTTP_HOST'] ?? 'Unknown_Host'));
        define('uri', trim((string) ($_SERVER['REQUEST_URI'] ?? '/'), '/'));
        define('ip', (string) ($_SERVER['REMOTE_ADDR'] ?? 'Unknown_IP'));

        define('debug', in_array(ip, []));
        date_default_timezone_set('Europe/London');

        if (debug) {
            error_reporting(-1);
            ini_set('display_errors', '1');
        }
        new core();
    }
} catch (Throwable $e) {
    error_handler::exception_handler($e);
    echo $e->getTraceAsString();
}
