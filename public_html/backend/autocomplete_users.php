<?php
// backend/autocomplete_users.php
// Returns JSON suggestions for jQuery UI autocomplete on users.
// Schema: users(id, username, password_hash, created_at)

require_once "config.php";

header('Content-Type: application/json; charset=utf-8');

$term = $_GET['term'] ?? '';
$term = trim($term);

$suggestions = [];

if ($term !== '') {
    $sql = "SELECT id, username
            FROM users
            WHERE username LIKE CONCAT('%', ?, '%')
            ORDER BY username
            LIMIT 10";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $term);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $suggestions[] = [
                "label" => $row['username'],   // what user sees
                "value" => (int)$row['id']     // what we put into the hidden/ID field
            ];
        }

        $stmt->close();
    }
}

echo json_encode($suggestions);
