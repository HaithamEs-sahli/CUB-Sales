<?php
require_once 'config.php';
$errors = [];
$listing_id = isset($_POST['listing_id']) ? trim($_POST['listing_id']) : null;
$bedrooms = isset($_POST['bedrooms']) ? trim($_POST['bedrooms']) : null;
$bathrooms = isset($_POST['bathrooms']) ? trim($_POST['bathrooms']) : null;
$deposit = isset($_POST['deposit']) ? trim($_POST['deposit']) : null;
$available_from = isset($_POST['available_from']) ? trim($_POST['available_from']) : null;
if ($post === null || $post === '') $errors[] = 'listing_id is required';
if ($post === null || $post === '') $errors[] = 'bedrooms is required';
if ($post === null || $post === '') $errors[] = 'bathrooms is required';
if ($post === null || $post === '') $errors[] = 'deposit is required';
if ($post === null || $post === '') $errors[] = 'available_from is required';
if (!empty($errors)) {
  echo '<h2>Input Error</h2><ul>';
  foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>';
  echo '</ul><p><a href="../maintenance.html">← Back to Maintenance</a></p>';
  exit;
}
$stmt = $conn->prepare('INSERT INTO housing_listings (listing_id, bedrooms, bathrooms, deposit, available_from) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('iiids', $listing_id, $bedrooms, $bathrooms, $deposit, $available_from);
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