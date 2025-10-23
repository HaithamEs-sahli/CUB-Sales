<?php
require __DIR__.'/_db.php';
$r = $mysqli->query("SELECT 1 AS ok");
$row = $r ? $r->fetch_assoc() : null;
echo ($row && $row['ok']==1) ? "DB_OK" : "DB_FAIL";
?>
