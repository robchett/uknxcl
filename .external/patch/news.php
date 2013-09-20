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

\db::connect();
\db::connect();

$res = \db::query('SELECT DISTINCT ID,Name FROM forum_subsection');

$update_statement = \db::prepare('UPDATE news SET title =:title WHERE title = :old_title');


while ($row = \db::fetch($res)) {

    $update_statement->execute(array('title' => $row->Name, 'old_title' => $row->ID));


}