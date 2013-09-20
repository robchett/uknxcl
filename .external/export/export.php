<?php
/*set_time_limit(0);

if (!isset($_REQUEST['start']) || !isset($_REQUEST['end']) || !isset($_REQUEST['bl']) || !isset($_REQUEST['tr'])) {

    */?><!--
    <form action="export.php">
        <label>Start Date<input type="text" name="start" value="<?php /*echo(isset($_REQUEST['start']) ? $_REQUEST['start'] : '2009-01-01') */?>"></label>
        <label>End Date<input type="text" name="end" value="<?php /*echo(isset($_REQUEST['end']) ? $_REQUEST['end'] : date('Y-m-d')) */?>"></label>
        <label>Bottom LEft (unused for now)<input type="text" name="bl" value="<?php /*echo(isset($_REQUEST['bl']) ? $_REQUEST['bl'] : '49.894634,-5.979858') */?>"></label>
        <label>Top Right (unused for now)<input type="text" name="tr" value="<?php /*echo(isset($_REQUEST['tr']) ? $_REQUEST['tr'] : '59.467408,1.57544') */?>"></label>
        <input type="submit" value="Go!"/>
    </form>
--><?php
/*} else {
    function __autoload($classname) {

        if (is_readable($filename = $_SERVER['DOCUMENT_ROOT'] . "/inc/object/" . $classname . ".php")) {
        } else if (is_readable($filename = $_SERVER['DOCUMENT_ROOT'] . "/inc/static/" . $classname . ".php")) {
        } else if (is_readable($filename = $_SERVER['DOCUMENT_ROOT'] . "/inc/form/" . $classname . ".php")) {
        } else {
            echo '<pre><p>Class not found ' . $classname . '</p><p>' . print_r(debug_backtrace(), 1) . '</p></pre>';
        }
        include_once($filename);
    }

    \db::connect();

    $bl = explode(',', $_REQUEST['bl']);
    $tr = explode(',', $_REQUEST['tr']);

//    $res = \db::query('SELECT fid,date FROM flight
//    WHERE maximum_north > :north
//    AND maximum_south > :south
//    AND maximum_east > :east
//    AND maximum_south > :west
//    AND date > :start
//    AND date < :end
//    AND dim > 1
//    ORDER BY date',
//    array(
//        'north'=>$tr[0],
//        'south'=>$bl[0],
//        'east'=>$tr[1],
//        'west'=>$tr[1],
//        'start'=>$_REQUEST['start'],
//        'end'=>$_REQUEST['end'],
//    ));


    $res = \db::query('SELECT fid,`date` FROM flight
    WHERE `date` > :start
    AND `date` < :end
    AND did > 1
    ORDER BY `date`',
        array(
            'start' => $_REQUEST['start'],
            'end' => $_REQUEST['end'],
        )
    );

    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/uploads/maps/' . time() . '.kml', 'w');

    fwrite($file, kml::get_kml_header());

    $current_year = '';
    $current_month = '';
    $year_open = false;
    $month_open = false;

    if (\db::num($res)) {
        while ($row = \db::fetch($res)) {

            if ($current_year != substr($row->date, 0, 4)) {
                if ($month_open) fwrite($file, kml::get_kml_folder_close());
                if ($year_open) fwrite($file, kml::get_kml_folder_close());
                fwrite($file, kml::get_kml_folder_open(substr($row->date, 0, 4)));
                $current_year = substr($row->date, 0, 4);
                $year_open = true;
                $month_open = false;
                $current_month = '';
            }
            if ($current_month != substr($row->date, 5, 2)) {
                if ($month_open) fwrite($file, kml::get_kml_folder_close());
                fwrite($file, kml::get_kml_folder_open(substr($row->date, 5, 2)));
                $current_month = substr($row->date, 5, 2);
                $month_open = true;
            }

            $track = new track();
            $track->id = $row->fid;
            $track->parse_IGC();
            if ($track->parsed) {
                fwrite($file, $track->generate_kml(true));
            }
        }

        if ($month_open) fwrite($file, kml::get_kml_folder_close());
        if ($year_open) fwrite($file, kml::get_kml_folder_close());

    }

    fwrite($file, kml::get_kml_footer());


}*/
