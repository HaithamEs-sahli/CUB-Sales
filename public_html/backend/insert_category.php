<?php
require __DIR__.'/../_db.php';
require __DIR__.'/../auth_check.php';
?>

<?php
require_once 'config.php';
$errors = [];
$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$description = isset($_POST['description']) ? trim($_POST['description']) : null;
if ($post === null || $post === '') $errors[] = 'name is required';
if ($post === null || $post === '') $errors[] = 'description is required';
if (!empty($errors)) {
  echo '<h2>Input Error</h2><ul>';
  foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>';
  echo '</ul><p><a href="../maintenance.html">← Back to Maintenance</a></p>';
  exit;
}
$stmt = $conn->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
$stmt->bind_param('ss', $name, $description);
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
