<?php
require "config.php";
if (isset($conn) && !$conn->connect_error) {
    echo "<p>✅ Connection OK to database ".htmlspecialchars($DB_NAME)."</p>";
} else {
    echo "<p>❌ Connection failed: ".htmlspecialchars($conn->connect_error)."</p>";
}
?>
