<?php
set_time_limit(0);

if (php_sapi_name() == 'cli') {
    define('load_core', false);
    $_SERVER['DOCUMENT_ROOT'] = '/var/www/vhosts/uknxcl/web';
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/index.php';

require_once($_SERVER['DOCUMENT_ROOT'] . '/.core/classes/auto_loader.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/.core/dependent/classes/auto_loader.php');
$autoloader = new \classes\auto_loader();


\classes\db::default_connection();

$file_name = $_SERVER['DOCUMENT_ROOT'] . '/uploads/maps/' . time() . '.kml';
$file = fopen($file_name, 'w');

$kml = new \classes\kml();
fwrite($file, $kml->get_kml_header());


$league = new \module\tables\object\league_table();
$league->options->set_year('1990-2016');
$league->options->set_flown_through('SO,SK,SJ,SP');
$league->options->set_dimensions(1);
$league->get_flights();

$current_year = '';
$current_month = '';
$year_open = false;
$month_open = false;

$total =  count($league->flights);
$cnt = 0;

echo "Count: " . count($league->flights);

$league->flights->iterate(function (\object\flight $flight) use ($total, &$cnt, &$file, &$kml, &$current_year, &$current_month, &$year_open, &$month_open) {
    echo "Exporting " . $flight->fid . ":$cnt of $total <br/>";
    echo str_repeat(' ',1024*1024);
    $cnt++;
    if ($current_year != substr($flight->date, 0, 4)) {
        if ($month_open) {
            $kml->get_kml_folder_close();
        }
        if ($year_open) {
            $kml->get_kml_folder_close();
        }
        $kml->get_kml_folder_open(substr($flight->date, 0, 4));
        $current_year = substr($flight->date, 0, 4);
        $year_open = true;
        $month_open = false;
        $current_month = '';
    }
    if ($current_month != substr($flight->date, 5, 2)) {
        if ($month_open) {
            $kml->get_kml_folder_close();
        }
        $kml->get_kml_folder_open(substr($flight->date, 5, 2));
        $current_month = substr($flight->date, 5, 2);
        $month_open = true;
    }

    $track = new \track\track();
    $track->id = $flight->fid;
    $track->parse_IGC();
    if ($track->parsed) {
        $kml->add($track->generate_kml(true));
    }

    if ($month_open) {
        $kml->get_kml_folder_close();
    }
    if ($year_open) {
        $kml->get_kml_folder_close();
    }

});

$kml->get_kml_footer();
$kml->compile(false, $file_name);
echo $file_name;
