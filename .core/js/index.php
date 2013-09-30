<?php

header('Content-type: text/javascript');

$output = "";

if (isset($core_files)) {
    foreach ($core_files as $file) {
        $output .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/.core/js/' . $file . '.js');
    }
}
$files = glob("./*.js", GLOB_MARK);
foreach ($files as $file) {
    $output .= file_get_contents($file);
}
//$output = preg_replace('/(\s|;|^|,)\/\/.*$/m','$1',$output);
//$output = preg_replace('/\s+/',' ',$output);

echo $output;
