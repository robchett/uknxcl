<?php
require_once('../index.php');
header("Content-type: text/css");
$css = '';
$files = glob('*.less');
foreach ($files as $file) {
    $css .= '@import "' . pathinfo($file, PATHINFO_FILENAME) . "\";\n";
}
lessc::$debug = true;
$lessc = new lessc();
$lessc->addImportDir(root . '/css/');
$less = $lessc->parse($css, null, 'dynamic_import.less');
//$less = lessc::cexecute($css, null);
echo $less;