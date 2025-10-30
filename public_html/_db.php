<?php
$mysqli = new mysqli('127.0.0.1', 'hessahli', 'wnZg038JFhX4fPS5', 'db_hessahli');
if ($mysqli->connect_errno) {
  http_response_code(500);
  die('DB connect error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
?>
