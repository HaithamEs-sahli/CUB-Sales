<?php require __DIR__.'/_db.php'; ?>
<!doctype html>
<meta charset="utf-8">
<title>Add Favorite</title>
<h2>Add Favorite</h2>
<form method="post" action="insert_favorite.php">
  <label>User:</label>
  <select name="user_id" required>
    <?php
      $res = $mysqli->query("SELECT user_id, display_name FROM user ORDER BY display_name");
      while($r = $res->fetch_assoc()){
        $id = (int)$r['user_id'];
        $name = htmlspecialchars($r['display_name']);
        echo "<option value='{$id}'>#{$id} – {$name}</option>";
      }
    ?>
  </select>

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

  <button type="submit">Add Favorite</button>
</form>
