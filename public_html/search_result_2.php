<?php
require __DIR__.'/_db.php';

$sql = "SELECT c.category_id, c.name, COUNT(lc.listing_id) AS cnt
        FROM category c
        LEFT JOIN listing_category lc ON lc.category_id = c.category_id
        GROUP BY c.category_id, c.name
        ORDER BY cnt DESC, c.name ASC";
$res = $mysqli->query($sql);
?>
<!doctype html><meta charset="utf-8"><title>Results: Listings per Category</title>
<link rel="stylesheet" href="style.css">
<h1>Listings per Category</h1>
<table border="1" cellpadding="6">
  <tr><th>Category ID</th><th>Name</th><th>Listings</th></tr>
  <?php while($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?= (int)$r['category_id'] ?></td>
      <td><?= htmlspecialchars($r['name']) ?></td>
      <td><?= (int)$r['cnt'] ?></td>
    </tr>
  <?php endwhile; ?>
</table>
<p><a href="search_form_2.php">← Back</a></p>
