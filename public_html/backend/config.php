<?php
$DB_HOST = 'localhost';
$DB_USER = 'hessahli';
$DB_PASS = 'wnZg038JFhX4fPS5';
$DB_NAME = 'db_hessahli';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
mysqli_set_charset($conn, 'utf8mb4');
?>