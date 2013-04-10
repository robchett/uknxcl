<?php
if (isset($_GET['id'])) {
    include '../phpsqlajax_dbinfo.php';
    execute("INSERT INTO Comps (cpid,Name,TaskName,Cords,StartTime,EndTime) VALUES ($_GET[id],'$_GET[cname]','$_GET[name]','$_GET[task]','$_GET[st]','$_GET[et]')");
    echo "Comp Added";
}
?>
