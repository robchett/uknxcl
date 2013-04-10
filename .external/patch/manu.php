<?php
function __autoload($classname) {

    if (is_readable($filename = $_SERVER['DOCUMENT_ROOT'] . "/inc/object/" . $classname . ".php")) {
    } else if (is_readable($filename = $_SERVER['DOCUMENT_ROOT'] . "/inc/static/" . $classname . ".php")) {
    } else if (is_readable($filename = $_SERVER['DOCUMENT_ROOT'] . "/inc/form/" . $classname . ".php")) {
    } else {
        echo '<pre><p>Class not found ' . $classname . '</p><p>' . print_r(debug_backtrace(), 1) . '</p></pre>';
    }
    include_once($filename);
}

db::connect();

$res = db::query('SELECT DISTINCT(mid) FROM glider');

$insert_statement = db::prepare('INSERT INTO manufacturer SET title=:title');
$update_statement = db::prepare('UPDATE glider SET manufacturer =:mid WHERE manufacturer = :title');


while ($row = db::fetch($res)) {

    $insert_statement->execute(array('title' => $row->manufacturer));

    $id = db::insert_id();

    $update_statement->execute(array('title' => $row->manufacturer, 'mid' => $id));


}