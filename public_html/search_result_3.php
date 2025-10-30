<?php
require __DIR__.'/_db.php';

$sql = "SELECT u.user_id, u.display_name,
               l.listing_id, l.title,
               f.created_at
        FROM favorite f
        JOIN user u    ON u.user_id = f.user_id
        JOIN listing l ON l.listing_id = f.listing_id
        ORDER BY f.created_at DESC";
$res = $mysqli->query($sql);
?>
<!doctype html><meta charset="utf-8"><title>Results: User Favorites</title>
<link rel="stylesheet" href="style.css">
<h1>User Favorites</h1>
<table border="1" cellpadding="6">
  <tr><th>User</th><th>Listing</th><th>Title</th><th>Favorited At</th></tr>
  <?php while($r = $res->fetch_assoc()): ?>
    <tr>
      <td>#<?= (int)$r['user_id'] ?> — <?= htmlspecialchars($r['display_name']) ?></td>
      <td>#<?= (int)$r['listing_id'] ?></td>
      <td><?= htmlspecialchars($r['title']) ?></td>
      <td><?= htmlspecialchars($r['created_at']) ?></td>
    </tr>
  <?php endwhile; ?>
</table>
<p><a href="search_form_3.php">← Back</a></p>
