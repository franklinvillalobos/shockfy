<?php
// auth_check.php — protege páginas y exige email verificado
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

// 1) Debe estar logueado
if (empty($_SESSION['logged_in'])) {
  header('Location: login.php'); exit;
}

// 2) Debe tener email verificado (intentamos refrescar desde BD si existe la columna)
if (empty($_SESSION['email_verified'])) {
  $dbPath = __DIR__ . '/db.php';
  if (file_exists($dbPath)) {
    require_once $dbPath;
    if (isset($pdo) && $pdo instanceof PDO && !empty($_SESSION['user_id'])) {
      try {
        $st = $pdo->prepare("SELECT email_verified FROM users WHERE id = ? LIMIT 1");
        $st->execute([$_SESSION['user_id']]);
        $val = $st->fetchColumn();
        if ($val !== false) {
          $_SESSION['email_verified'] = (bool)$val;
        }
      } catch (Throwable $e) { /* si la columna no existe o error, ignoramos */ }
    }
  }

  if (empty($_SESSION['email_verified'])) {
    header('Location: welcome.php'); exit;
  }
}
