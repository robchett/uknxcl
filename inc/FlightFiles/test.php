<?php
include('./track.php');
include('./file_convert.php');
include('../phpsqlajax_dbinfo.php');
$flight = new track();

$flight->parse_IGC();
$flight->pre_calc();
$flight->calculate();

$flight->generate_kml();
$flight->generate_kml_earth();
$flight->generate_js();

unset($flight->distance_map);
//echo '<p><pre>'.print_r($flight,true).'</pre></p>'."\n";
