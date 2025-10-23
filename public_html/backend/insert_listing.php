<?php
require_once 'config.php';
$errors = [];
$poster_id = isset($_POST['poster_id']) ? trim($_POST['poster_id']) : null;
$title = isset($_POST['title']) ? trim($_POST['title']) : null;
$description = isset($_POST['description']) ? trim($_POST['description']) : null;
$price_decimal = isset($_POST['price_decimal']) ? trim($_POST['price_decimal']) : null;
$location = isset($_POST['location']) ? trim($_POST['location']) : null;
$status = isset($_POST['status']) ? trim($_POST['status']) : null;
if ($post === null || $post === '') $errors[] = 'poster_id is required';
if ($post === null || $post === '') $errors[] = 'title is required';
if ($post === null || $post === '') $errors[] = 'description is required';
if ($post === null || $post === '') $errors[] = 'price_decimal is required';
if ($post === null || $post === '') $errors[] = 'location is required';
if ($post === null || $post === '') $errors[] = 'status is required';
if (!empty($errors)) {
  echo '<h2>Input Error</h2><ul>';
  foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>';
  echo '</ul><p><a href="../maintenance.html">← Back to Maintenance</a></p>';
  exit;
}
$stmt = $conn->prepare('INSERT INTO listings (poster_id, title, description, price_decimal, location, status) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->bind_param('issdss', $poster_id, $title, $description, $price_decimal, $location, $status);
if ($stmt->execute()) {
  echo '<h2>Success</h2><p>Record inserted with ID: '. $stmt->insert_id .'</p>';
  echo '<p><a href="../maintenance.html">← Back to Maintenance</a></p>';
} else {
  echo '<h2>Database Error</h2><p>'. htmlspecialchars($stmt->error) .'</p>';
  echo '<p><a href="../maintenance.html">← Back to Maintenance</a></p>';
}
$stmt->close();
$conn->close();
?>