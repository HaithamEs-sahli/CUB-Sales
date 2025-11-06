<?php
// Unified DB connector for all pages
$DB_HOST = '127.0.0.1';          // use TCP to avoid socket quirks
$DB_USER = 'hessahli';
$DB_PASS = 'wnZg038JFhX4fPS5';   // <-- EXACT password
$DB_NAME = 'db_hessahli';
$DB_PORT = 3306;

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
if ($mysqli->connect_errno) {
  http_response_code(500);
  die('DB connect error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

// Provide $conn as an alias for code that expects it
$conn = $mysqli;

