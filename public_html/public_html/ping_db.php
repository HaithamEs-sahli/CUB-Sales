<?php
$mysqli = new mysqli('127.0.0.1','root','rootpwd','cubsales'); // adjust if needed
if ($mysqli->connect_errno) { http_response_code(500); die("DB connect error: ".$mysqli->connect_error); }
$r = $mysqli->query("SELECT 1 AS ok");
$row = $r ? $r->fetch_assoc() : null;
echo $row && $row['ok']==1 ? "DB_OK" : "DB_FAIL";
