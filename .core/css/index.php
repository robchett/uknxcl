<?php
header("Content-type: text/css");
define('load_core', false);
include($_SERVER['DOCUMENT_ROOT'] . '/index.php');
$css = '';
$files = glob(root . '/.core/css/*.less');
$time_stamp = 0;
foreach ($files as $file) {
    $css .= '@import "' . pathinfo($file, PATHINFO_FILENAME) . "\";\n";
    if (($time = filemtime($file)) > $time_stamp) {
        $time_stamp = $time;
    }
}
$files = glob(root . '/css/*.less');
foreach ($files as $file) {
    $css .= '@import "' . pathinfo($file, PATHINFO_FILENAME) . "\";\n";
    if (($time = filemtime($file)) > $time_stamp) {
        $time_stamp = $time;
    }
}
$file_path = root . '/.cache/' . $time_stamp . '.css';
if (file_exists($file_path)) {
    echo file_get_contents($file_path);
} else {
    \classes\lessc::$debug = true;
    $lessc = new \classes\lessc();
    $lessc->addImportDir(root . '/css/');
    $lessc->addImportDir(root . '/.core/css/');
    $less = $lessc->parse($css, null, 'dynamic_import.less');
//$less = lessc::cexecute($css, null);
    file_put_contents($file_path, $less);
    echo $less;
}