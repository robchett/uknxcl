<?php
session_start();
define('admin', isset($_SESSION['admin']));
define('root', $_SERVER['DOCUMENT_ROOT']);
define('core_dir', root . '/.core');
define('ajax', isset($_REQUEST['module']));

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
    define ('ie', true);
    define ('ie_ver', 0);
} else {
    define ('ie', false);
    define ('ie_ver', 0);
}

function __autoload($classname) {

    $classname = str_replace(array('_iterator', '_array'), '', $classname);

    if (is_readable($filename = root . "/inc/object/" . $classname . ".php")) {
    } else if (is_readable($filename = root . "/inc/module/" . $classname . "/" . $classname . ".php")) {
    } else if (is_readable($filename = root . "/inc/forms/" . $classname . ".php")) {
    } else if (is_readable($filename = root . "/inc/object/form/" . $classname . ".php")) {
    } else if (is_readable($filename = core_dir . '/' . $classname . ".php")) {
    } else if (is_readable($filename = core_dir . '/' . 'classes/' . $classname . ".php")) {
    } else {
        echo '<pre><p>Class not found ' . $classname . '</p><p>' . print_r(debug_backtrace(), 1) . '</p></pre>';
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

