<?php
// backend/autocomplete_all.php
require_once "config.php";

header('Content-Type: application/json; charset=utf-8');

// Example: get all listing titles (you can change this to another table/column)
$sql = "SELECT title FROM listing";
$result = $conn->query($sql);

$suggestions = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['title'];
    }
}

echo json_encode($suggestions);
