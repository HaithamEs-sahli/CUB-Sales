<?php
require_once 'config.php';
$errors = [];
$netid = isset($_POST['netid']) ? trim($_POST['netid']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$display_name = isset($_POST['display_name']) ? trim($_POST['display_name']) : null;
if ($post === null || $post === '') $errors[] = 'netid is required';
if ($post === null || $post === '') $errors[] = 'email is required';
if ($post === null || $post === '') $errors[] = 'display_name is required';
if (!empty($errors)) {
  echo '<h2>Input Error</h2><ul>';
  foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>';
  echo '</ul><p><a href="../maintenance.html">← Back to Maintenance</a></p>';
  exit;
}
$stmt = $conn->prepare('INSERT INTO users (netid, email, display_name) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $netid, $email, $display_name);
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