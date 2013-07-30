<?php
session_start();
define('admin', isset($_SESSION['admin']));
define('root', $_SERVER['DOCUMENT_ROOT']);
define('core_dir', root . '/.core');
define('ajax', isset($_REQUEST['module']));

define('host', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'Unknown_Host' );
define('uri', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'Unknown_URI' );

define('ip', isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown_IP' );

define('dev', strpos(host,'local.com') !== false || strpos(host,'dev.'));
define('debug', ip == '2.26.220.251');

if(debug) {
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
function __autoload($classname) {

    $classname = str_replace(array('_iterator', '_array'), '', $classname);

    if        (is_readable($filename = root . "/inc/object/" . $classname . ".php")) {
    } else if (is_readable($filename = root . "/inc/module/" . $classname . "/" . $classname . ".php")) {
    } else if (is_readable($filename = root . "/inc/forms/" . $classname . ".php")) {
    } else if (is_readable($filename = root . "/inc/object/form/" . $classname . ".php")) {
    } else if (is_readable($filename = core_dir . '/' . $classname . ".php")) {
    } else if (is_readable($filename = core_dir . '/classes/' . $classname . ".php")) {
    } else if (is_readable($filename = core_dir . '/interfaces/' . $classname . ".php")) {
    } else {
        echo '<pre><p>Class not found ' . $classname . $filename . '</p><p>' . print_r(debug_backtrace(), 1) . '</p></pre>';
        return false;
    }
    require_once($filename);
}

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        if (strpos($errfile, 'xdebug') !== 0) {
            $error = '<div class="error_message mysql"><p>Error #' . $errno . ' "' . $errstr . '" in ' . $errfile . ' on line ' . $errline . '</p>' . core::get_backtrace() . '</div>';
            if (ajax) {
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