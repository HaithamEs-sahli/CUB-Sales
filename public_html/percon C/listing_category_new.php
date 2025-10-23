<?php require __DIR__.'/_db.php'; ?>
<!doctype html>
<meta charset="utf-8">
<title>Link Listing ↔ Category</title>
<h2>Link Listing ↔ Category</h2>
<form method="post" action="insert_listing_category.php">
  <label>Listing:</label>
  <select name="listing_id" required>
    <?php
      $res = $mysqli->query("SELECT listing_id, title FROM listing ORDER BY created_at DESC");
      while($r = $res->fetch_assoc()){
        $id = (int)$r['listing_id'];
        $title = htmlspecialchars($r['title']);
        echo "<option value='{$id}'>#{$id} – {$title}</option>";
      }
    ?>
  </select>

  <label>Category:</label>
  <select name="category_id" required>
    <?php
      $res = $mysqli->query("SELECT category_id, name FROM category ORDER BY name");
      while($r = $res->fetch_assoc()){
        $id = (int)$r['category_id'];
        $name = htmlspecialchars($r['name']);
        echo "<option value='{$id}'>#{$id} – {$name}</option>";
      }
    ?>
  </select>

  <button type="submit">Add</button>
</form>
