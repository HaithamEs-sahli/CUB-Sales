<?php
// search_detail_1.php — single listing detail for "Listings by User"
require_once 'config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// 1) validate id
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
  http_response_code(400);
  echo "<p>Invalid or missing listing id.</p>";
  exit;
}
$listing_id = (int)$_GET['id'];

// 2) main record with joins (adjust column/table names if needed)
$sql = "
SELECT l.*, 
       u.username, u.email,
       h.address, h.city, h.country
FROM listings l
LEFT JOIN users u     ON u.user_id = l.user_id
LEFT JOIN housing h   ON h.housing_id = l.housing_id
WHERE l.listing_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $listing_id);
$stmt->execute();
$listing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$listing) {
  echo "<p>No record found for id ".h($listing_id).".</p>";
  exit;
}

// 3) categories for this listing
$cats = [];
$sqlCats = "
SELECT c.category_id, c.name
FROM rel_listing_category r
JOIN categories c ON c.category_id = r.category_id
WHERE r.listing_id = ?";
$stmt = $conn->prepare($sqlCats);
$stmt->bind_param('i', $listing_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $cats[] = $row;
$stmt->close();

// 4) favorite count
$fav_count = 0;
$sqlFav = "SELECT COUNT(*) AS cnt FROM favorites WHERE listing_id = ?";
$stmt = $conn->prepare($sqlFav);
$stmt->bind_param('i', $listing_id);
$stmt->execute();
$stmt->bind_result($fav_count);
$stmt->fetch();
$stmt->close();

// 5) render
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Listing Details</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .detail { max-width: 960px; margin: 2rem auto; background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
    .detail h1 { margin-top: 0; }
    table.meta { width: 100%; border-collapse: collapse; }
    table.meta th, table.meta td { text-align: left; padding: .5rem .75rem; border-bottom: 1px solid #eee; vertical-align: top; }
    .pill { display:inline-block; padding:.2rem .5rem; border:1px solid #ddd; border-radius:999px; margin-right:.25rem; }
    .actions { margin-top: 1rem; }
  </style>
</head>
<body>
  <div class="detail">
    <h1><?= h($listing['title'] ?? 'Listing #'.$listing_id) ?></h1>
    <p><?= nl2br(h($listing['description'] ?? '')) ?></p>

    <table class="meta">
      <tr><th>Listing ID</th><td><?= h($listing['listing_id']) ?></td></tr>
      <tr><th>Price</th><td><?= h($listing['price'] ?? '') ?></td></tr>
      <tr><th>Owner</th><td><?= h($listing['username'] ?? '') ?> <?= $listing['email'] ? '('.h($listing['email']).')' : '' ?></td></tr>
      <tr><th>Created</th><td><?= h($listing['created_at'] ?? '') ?></td></tr>
      <?php if (!empty($listing['address']) || !empty($listing['city']) || !empty($listing['country'])): ?>
      <tr><th>Location</th><td><?= h(trim(($listing['address'] ?? '').', '.($listing['city'] ?? '').', '.($listing['country'] ?? ''), ' ,')) ?></td></tr>
      <?php endif; ?>
      <tr><th>Favorites</th><td><?= h($fav_count) ?></td></tr>
      <tr><th>Categories</th><td>
        <?php if ($cats): foreach ($cats as $c): ?>
          <span class="pill"><?= h($c['name']) ?></span>
        <?php endforeach; else: ?>
          <em>None</em>
        <?php endif; ?>
      </td></tr>
    </table>

    <div class="actions">
      <?php
        $back = $_SERVER['HTTP_REFERER'] ?? 'search_result_1.php';
        echo '<a href="'.h($back).'">&larr; Back to Results</a>';
      ?>
    </div>
  </div>
</body>
</html>
