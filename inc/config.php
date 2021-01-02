<?php

use classes\auto_loader;
use classes\ini;

define('root', $_SERVER['DOCUMENT_ROOT']);
define('ajax', isset($_REQUEST['module']));
define('host', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'Unknown_Host');
define('uri', isset($_SERVER['REQUEST_URI']) ? trim($_SERVER['REQUEST_URI'], '/') : 'Unknown_URI');
define('ip', isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown_IP');


if (!class_exists('\\classes\\autoloader', false)) {
    require_once(root . '/inc/classes/auto_loader.php');
}
$auto_loader = new auto_loader();

set_error_handler(['\classes\error_handler', 'handle_error']);
register_shutdown_function(['\classes\error_handler', 'fatal_handler']);

define('debug', in_array(ip, ini::get('developers', 'ip', [])));

date_default_timezone_set(ini::get('zone', 'time', 'Europe/London'));

if (debug) {
    error_reporting(-1);
    ini_set('display_errors', '1');
}

if (!defined('load_core') || load_core) {
    $core = new core();
}