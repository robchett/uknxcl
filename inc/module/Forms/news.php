<?php
@include_once '../../phpsqlajax_dbinfo.php';
$result = execute("SELECT ID FROM forum_post ORDER BY ID DESC LIMIT 1");
$id = mysql_fetch_array($result);
$id = $id [0] + 1;
$sql = "INSERT INTO forum_subsection (Super,Name,Description,Creator)
        VALUES (4,'$_POST[title]','$_POST[text]','$_POST[name]')";
$result = execute($sql);
$result = execute("SELECT ID FROM forum_subsection ORDER BY ID DESC LIMIT 1");
$id2 = mysql_fetch_array($result);
$sql = "INSERT INTO forum_post (Section,Poster,Post,Topic)
        VALUES (4,'$_POST[name]','$_POST[text]',$id2[0])";
$result = execute($sql);
echo "
    <script language='javascript' type='text/javascript'>
    window.top.window.stopUpload('post added','AdminWriteHere');
    </script>";
