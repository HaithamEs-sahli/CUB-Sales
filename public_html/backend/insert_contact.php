<?php
require __DIR__.'/../_db.php';
require __DIR__.'/../auth_check.php';
?>

<?php
require_once 'config.php';
$errors = [];
$user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : null;
$type = isset($_POST['type']) ? trim($_POST['type']) : null;
$value = isset($_POST['value']) ? trim($_POST['value']) : null;
if ($post === null || $post === '') $errors[] = 'user_id is required';
if ($post === null || $post === '') $errors[] = 'type is required';
if ($post === null || $post === '') $errors[] = 'value is required';
if (!empty($errors)) {
  echo '<h2>Input Error</h2><ul>';
  foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>';
  echo '</ul><p><a href="../maintenance.html">← Back to Maintenance</a></p>';
  exit;
}
$stmt = $conn->prepare('INSERT INTO contacts (user_id, type, value) VALUES (?, ?, ?)');
$stmt->bind_param('iss', $user_id, $type, $value);
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
