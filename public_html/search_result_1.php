<?php
require __DIR__.'/_db.php';
$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
if (!$user_id) { http_response_code(400); exit('Bad user_id'); }

$sql = "SELECT listing_id, title, price_decimal, status, created_at
        FROM listing
        WHERE poster_id = ?
        ORDER BY created_at DESC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html><meta charset="utf-8"><title>Results: Listings by User</title>
<link rel="stylesheet" href="style.css">
<h1>Listings by User #<?= htmlspecialchars($user_id) ?></h1>
<table border="1" cellpadding="6">
  <tr><th>ID</th><th>Title</th><th>Price</th><th>Status</th><th>Created</th></tr>
  <?php while($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?= (int)$r['listing_id'] ?></td>
      <td><?= htmlspecialchars($r['title']) ?></td>
      <td><?= htmlspecialchars($r['price_decimal']) ?></td>
      <td><?= htmlspecialchars($r['status']) ?></td>
      <td><?= htmlspecialchars($r['created_at']) ?></td>
    </tr>
  <?php endwhile; ?>
</table>
<p><a href="search_form_1.php">← Back</a></p>
