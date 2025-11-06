<?php
require __DIR__.'/_db.php';
require __DIR__.'/auth_check.php';
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CUB Sales — Maintenance</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
  <div class="container row">
    <div class="brand">
      <img src="img/logo.png" alt="CUB Sales logo">
      <div class="name">CUB Sales</div>
    </div>
    <div class="userlinks">
      <a href="index.html">Home</a>
      <a href="maintenance.html">Maintenance</a>
      <a href="imprint.html">Imprint</a>
    </div>
  </div>
</header>

<main class="container" style="padding:40px 0;">
  <h1>Maintenance Page (Entities)</h1>
  <p>Input pages for Assignment 5 — Person 1 & 2 scope.</p>
  <section class="section">
    <h2>Entity Input</h2>
    <ul>
      <li><a href="input_user.html">User</a></li>
      <li><a href="input_category.html">Category</a></li>
      <li><a href="input_listing.html">Listing</a></li>
      <li><a href="input_housing_listing.html">Housing Listing (ISA)</a></li>
      <li><a href="input_sales_listing.html">Sales Listing (ISA)</a></li>
      <li><a href="input_contact.html">Contact</a></li>
    </ul>
  </section>
  <section class="section">
    <p style="color:#6b7280;">Relationship inputs are excluded (Person 3).</p>
  </section>
</main>

<footer class="footer">
  <div class="copy">© 2025 CUB Sales — Constructor University Student Project</div>
</footer>
</body>
</html>
