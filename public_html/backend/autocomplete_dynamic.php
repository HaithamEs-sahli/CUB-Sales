<?php
// backend/autocomplete_dynamic.php
require_once "config.php";

header('Content-Type: application/json; charset=utf-8');

$term = $_GET['term'] ?? '';
$term = trim($term);

$suggestions = [];

if ($term !== '') {
    // Example: search in listing titles
    $sql = "SELECT DISTINCT title 
            FROM listing 
            WHERE title LIKE CONCAT('%', ?, '%')
            LIMIT 10";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $term);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $suggestions[] = $row['title'];
        }
        $stmt->close();
    }
}

echo json_encode($suggestions);
