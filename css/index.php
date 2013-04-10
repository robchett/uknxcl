<?php

header("Content-type: text/css");
$css = '';
$files = glob('*.css');
foreach ($files as $file) {
    $css .= file_get_contents($file);
}
echo $css;