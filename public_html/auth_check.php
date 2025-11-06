<?php
// auth_check.php — include at the top of protected pages
session_start();
if (empty($_SESSION['user'])) {
  header('Location: /~hessahli/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
  exit();
}
