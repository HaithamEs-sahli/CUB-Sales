<?php
session_start();
require __DIR__.'/_db.php';
// If your _db.php uses $conn instead of $mysqli, uncomment:
// if (!isset($mysqli) && isset($conn)) { $mysqli = $conn; }

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $err = 'Invalid request. Please retry.';
  } else {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $stmt = $mysqli->prepare('SELECT id, username, password_hash FROM users WHERE username=?');
    if ($stmt) {
      $stmt->bind_param('s', $u);
      $stmt->execute();
      $res = $stmt->get_result();
      if ($row = $res->fetch_assoc()) {
        if (password_verify($p, $row['password_hash'])) {
          session_regenerate_id(true);
          $_SESSION['user'] = ['id' => (int)$row['id'], 'username' => $row['username']];
          $dest = $_GET['redirect'] ?? 'maintenance.php';
          header('Location: ' . $dest);
          exit();
        }
      }
    }
    $err = 'Invalid username or password.';
  }
}
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — CUB-Sales</title>
<style>
  body { font-family: system-ui, Arial, sans-serif; margin: 2rem; }
  .card { max-width: 380px; margin: auto; padding: 1.25rem; border: 1px solid #ddd; border-radius: 12px; }
  label { display:block; margin-top: .75rem; }
  input[type=text], input[type=password]{ width:100%; padding:.6rem; border:1px solid #ccc; border-radius:8px; }
  button { margin-top:1rem; padding:.6rem 1rem; border:0; border-radius:8px; cursor:pointer; }
  .error { color:#b00020; margin:.5rem 0; }
</style>
</head><body>
  <div class="card">
    <h2>Admin Login</h2>
    <?php if ($err): ?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
    <form method="post" action="">
      <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'])?>">
      <label>Username <input name="username" type="text" required autofocus></label>
      <label>Password <input name="password" type="password" required></label>
      <button type="submit">Log in</button>
    </form>
  </div>
</body></html>
