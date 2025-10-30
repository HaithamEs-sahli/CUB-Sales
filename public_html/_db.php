<?php
$mysqli = new mysqli('127.0.0.1', 'root', 'rootpwd', 'cubsales');
if ($mysqli->connect_errno) {
  http_response_code(500);
  die('DB connect error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
?>
