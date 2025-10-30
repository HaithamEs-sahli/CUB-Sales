<?php
require_once 'config.php';
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) { http_response_code(400); echo "<p>Invalid id.</p>"; exit; }
$listing_id = (int)$_GET['id'];

$sql = "SELECT l.*, u.username, u.email
        FROM listings l
        LEFT JOIN users u ON u.user_id = l.user_id
        WHERE l.listing_id = ?";
$stmt = $conn->prepare($sql); $stmt->bind_param('i',$listing_id); $stmt->execute();
$listing = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$listing){ echo "<p>No record found.</p>"; exit; }

$stmt=$conn->prepare("SELECT COUNT(*) FROM favorites WHERE listing_id=?");
$stmt->bind_param('i',$listing_id); $stmt->execute(); $stmt->bind_result($fav_count); $stmt->fetch(); $stmt->close();

$cats=[]; $stmt=$conn->prepare("SELECT c.name FROM rel_listing_category r JOIN categories c ON c.category_id=r.category_id WHERE r.listing_id=?");
$stmt->bind_param('i',$listing_id); $stmt->execute(); $r=$stmt->get_result(); while($row=$r->fetch_assoc()) $cats[]=$row['name']; $stmt->close();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Favorite Listing</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="detail">
  <h1><?= h($listing['title'] ?? 'Listing #'.$listing_id) ?></h1>
  <p><?= nl2br(h($listing['description'] ?? '')) ?></p>
  <p><strong>Owner:</strong> <?= h($listing['username'] ?? '') ?> <?= $listing['email'] ? '('.h($listing['email']).')' : '' ?></p>
  <p><strong>Categories:</strong> <?= $cats ? h(implode(', ', $cats)) : '<em>None</em>' ?></p>
  <p><strong>Favorites:</strong> <?= h($fav_count) ?></p>
  <p><a href="<?= h($_SERVER['HTTP_REFERER'] ?? 'search_result_3.php') ?>">&larr; Back to Results</a></p>
</div>
</body></html>
