<?php
require_once 'config.php';
$errors = [];
$listing_id = isset($_POST['listing_id']) ? trim($_POST['listing_id']) : null;
$condition = isset($_POST['condition']) ? trim($_POST['condition']) : null;
$brand = isset($_POST['brand']) ? trim($_POST['brand']) : null;
$model = isset($_POST['model']) ? trim($_POST['model']) : null;
$warranty_months = isset($_POST['warranty_months']) ? trim($_POST['warranty_months']) : null;
if ($post === null || $post === '') $errors[] = 'listing_id is required';
if ($post === null || $post === '') $errors[] = 'condition is required';
if ($post === null || $post === '') $errors[] = 'brand is required';
if ($post === null || $post === '') $errors[] = 'model is required';
if ($post === null || $post === '') $errors[] = 'warranty_months is required';
if (!empty($errors)) {
  echo '<h2>Input Error</h2><ul>';
  foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>';
  echo '</ul><p><a href="../maintenance.html">← Back to Maintenance</a></p>';
  exit;
}
$stmt = $conn->prepare('INSERT INTO sales_listings (listing_id, condition, brand, model, warranty_months) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('isssi', $listing_id, $condition, $brand, $model, $warranty_months);
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