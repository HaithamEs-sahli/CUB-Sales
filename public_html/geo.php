<?php
// geo.php — Linked Services (HW10), CUB Sales

// Step 1: detect client IP
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

// Handle proxy headers (optional)
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $forwardedIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $candidate = trim($forwardedIps[0]);
    if (filter_var($candidate, FILTER_VALIDATE_IP)) {
        $ip = $candidate;
    }
}

// Step 2: IP → Geolocation lookup using ipinfo.io
$token = ''; // optional: add your ipinfo token here

// Function to check if IP is public (not private/reserved)
function is_public_ip($ip) {
    return filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    ) !== false;
}

$lat = 0.0;
$lon = 0.0;
$city = '';
$country = '';
$error = null;

// If IP is public → use it directly
// If IP is private (172.x, 10.x, 192.168.x) → use fallback (server public IP)
if (is_public_ip($ip)) {
    $url = $token
        ? "https://ipinfo.io/{$ip}/json?token={$token}"
        : "https://ipinfo.io/{$ip}/json";
} else {
    $url = $token
        ? "https://ipinfo.io/json?token={$token}"
        : "https://ipinfo.io/json";
}

$response = @file_get_contents($url);

if ($response === false) {
    $error = "Could not reach the geolocation service.";
} else {
    $data = json_decode($response, true);

    if (isset($data['loc'])) {
        $parts = explode(',', $data['loc']);
        if (count($parts) === 2) {
            $lat = (float)$parts[0];
            $lon = (float)$parts[1];
        } else {
            $error = "Location data format invalid.";
        }
    } else {
        $error = "Location not available for this IP.";
    }

    $city = $data['city'] ?? '';
    $country = $data['country'] ?? '';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CUB Sales — Your Region</title>

  <link rel="stylesheet" href="style.css">

  <!-- Leaflet CSS -->
  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
  />

  <style>
    #map {
      height: 420px;
      width: 100%;
      border-radius: 8px;
      border: 1px solid #ddd;
      margin-top: 1rem;
    }
    .location-wrapper { margin: 2rem 0 3rem 0; }
    .location-meta { margin-top: 0.5rem; font-size: 0.95rem; }
    .location-meta strong { font-weight: 600; }
    .location-error { color: #c0392b; margin-top: 0.5rem; }
  </style>
</head>
<body>

<header class="topbar">
  <div class="container row">
    <div class="brand">
      <img src="img/logo.png" alt="CUB Sales logo">
      <div class="name">CUB Sales</div>
    </div>
    <form class="search" action="search_result_1.php" method="get">
      <select name="cat">
        <option value="">All Categories</option>
        <option>Electronics</option>
        <option>Fashion</option>
        <option>Home &amp; Garden</option>
        <option>Collectibles</option>
        <option>Sports</option>
      </select>
      <input type="search" name="q" placeholder="Search for anything">
      <button type="submit">Search</button>
    </form>
    <div class="userlinks">
      <a href="login.php">Sign in</a>
      <a href="#">Deals</a>
      <a href="#">Help &amp; Contact</a>
      <a href="#">Cart</a>
    </div>
  </div>
</header>

<nav class="navbar">
  <div class="container">
    <div class="links">
      <a href="index.html">Home</a>
      <a href="search_form_1.php">Search Listings by User</a>
      <a href="search_form_2.php">Listings per Category</a>
      <a href="search_form_3.php">User Favorites</a>
      <a href="geo.php">Your Region</a>
      <a href="imprint.html">Imprint</a>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container wrap">
    <div>
      <h1>Your region, on the map.</h1>
      <p>This linked service takes your IP address, obtains approximate geo-coordinates
         from ipinfo.io, and displays them on an interactive map using Leaflet.</p>
    </div>
  </div>
</section>

<main class="container">
  <section class="section location-wrapper">
    <h2>Detected Region</h2>

    <div class="location-meta">
      <p><strong>Your IP address:</strong>
        <?php echo htmlspecialchars($ip); ?>
      </p>

      <?php if ($city || $country): ?>
        <p><strong>Approximate location:</strong>
          <?php echo htmlspecialchars(trim("$city $country")); ?>
        </p>
      <?php endif; ?>

      <?php if ($error): ?>
        <p class="location-error"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
    </div>

    <div id="map"></div>
  </section>
</main>

<footer class="footer">
  <div class="container cols">
    <div>
      <h4>Buy</h4>
      <a href="#">Registration</a>
      <a href="#">Bidding &amp; buying</a>
      <a href="#">Buyer protection</a>
    </div>
    <div>
      <h4>Sell</h4>
      <a href="#">Start selling</a>
      <a href="#">Seller fees</a>
      <a href="#">Shipping center</a>
    </div>
    <div>
      <h4>About</h4>
      <a href="imprint.html">Imprint</a>
      <a href="#">Terms &amp; Conditions</a>
      <a href="#">Privacy</a>
    </div>
  </div>
  <div class="container">
    <p>&copy; <?php echo date('Y'); ?> CUB Sales — Student project at Constructor University.</p>
  </div>
</footer>

<!-- Leaflet JS -->
<script
  src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
  integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
  crossorigin=""
></script>

<script>
// Injected values
const lat     = <?php echo json_encode($lat); ?>;
const lon     = <?php echo json_encode($lon); ?>;
const ip      = <?php echo json_encode($ip); ?>;
const city    = <?php echo json_encode($city); ?>;
const country = <?php echo json_encode($country); ?>;

// Validate coordinates
const hasCoords =
  typeof lat === "number" &&
  typeof lon === "number" &&
  !isNaN(lat) && !isNaN(lon) &&
  (lat !== 0 || lon !== 0);

// Map center fallback
const centerLat = hasCoords ? lat : 20;
const centerLon = hasCoords ? lon : 0;
const zoomLevel = hasCoords ? 9 : 2;

const map = L.map('map').setView([centerLat, centerLon], zoomLevel);

// OSM tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Marker + popup if coords exist
if (hasCoords) {
  const popup = `<b>IP:</b> ${ip}<br>${city} ${country}`;
  L.marker([lat, lon]).addTo(map)
    .bindPopup(popup)
    .openPopup();
}
</script>

</body>
</html>
