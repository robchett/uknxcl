<?php
session_start();
define('admin', isset($_SESSION['admin']));
define('root', $_SERVER['DOCUMENT_ROOT']);
define('core_dir', root . '/.core');
define('ajax', isset($_REQUEST['module']));

define('host', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'Unknown_Host');
define('uri', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'Unknown_URI');

define('ip', isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown_IP');

define('dev', strpos(host, 'local.com') !== false || strpos(host, 'dev.'));
define('debug', ip == '2.26.220.251');
date_default_timezone_set('Europe/London');
if (debug) {
    error_reporting(-1);
    ini_set('display_errors', '1');
}
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
    define ('ie', true);
    define ('ie_ver', 0);
} else {
    define ('ie', false);
    define ('ie_ver', 0);
}

include(root . '/.core/classes/auto_loader.php');
$auto_loader = new auto_loader();

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        if (strpos($errfile, 'xdebug') !== 0) {
            $error = '<div class="error_message mysql"><p>Error #' . $errno . ' "' . $errstr . '" in ' . $errfile . ' on line ' . $errline . '</p>' . \core::get_backtrace() . '</div>';
            if (ajax) {
                require_once(root . '/.core/classes/ajax.php');
                ajax::inject('body', 'append', $error);
            } else {
                echo $error;
            }
        }
    }
);
if (!defined('load_core') || load_core) {
    include(core_dir . '/core.php');
    $core = new core();
}