<?php
header("Content-type: text/css");
define('load_core', false);
include($_SERVER['DOCUMENT_ROOT'] . '/index.php');
$css = '';
$files = glob(root . '/.core/css/*.less');
foreach ($files as $file) {
    $css .= '@import "' . pathinfo($file, PATHINFO_FILENAME) . "\";\n";
}
$files = glob(root . '/css/*.less');
foreach ($files as $file) {
    $css .= '@import "' . pathinfo($file, PATHINFO_FILENAME) . "\";\n";
}
\classes\lessc::$debug = true;
$lessc = new \classes\lessc();
$lessc->addImportDir(root . '/css/');
$lessc->addImportDir(root . '/.core/css/');
$less = $lessc->parse($css, null, 'dynamic_import.less');
//$less = lessc::cexecute($css, null);
echo $less;