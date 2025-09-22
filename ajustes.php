<?php
require 'db.php';
require 'auth.php';
include 'header.php';
include 'sidebar.php';

// Obtener datos del usuario desde la sesión
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT full_name, username, currency_pref FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$current_currency = $user['currency_pref'] ?? 'S/.';

// Obtener mensajes de sesión si existen
$success_msg = $_SESSION['ajustes_success'] ?? '';
$error_msg   = $_SESSION['ajustes_error'] ?? '';
// Limpiar mensajes para no mostrarlos otra vez
unset($_SESSION['ajustes_success'], $_SESSION['ajustes_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ajustes</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* Ocultar la lupa del sidebar solo en ajustes.php */
    .sidebar li:first-child {
      display: none;
    }

    .settings-wrapper {
      max-width: 700px;
      margin: 80px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,.08);
      transition: background 0.3s, color 0.3s;
    }
    body.dark .settings-wrapper { background: #2c2c2c; color: #f1f1f1; }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 26px;
      color: #333;
      transition: color 0.3s;
    }
    body.dark h2 { color: #f1f1f1; }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #555;
      transition: color 0.3s;
    }
    body.dark label { color: #ccc; }

    input[type="text"], select {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 18px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      transition: background 0.3s, color 0.3s, border 0.3s;
    }
    body.dark input[type="text"], body.dark select {
      background: #3a3a3a;
      color: #fff;
      border: 1px solid #666;
    }

    button {
      display: inline-block;
      padding: 10px 20px;
      background: #3498db;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background .3s;
    }
    button:hover { background: #2980b9; }

    /* Mensajes de alerta */
    .alert {
      padding: 12px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .alert-success { background: #2ecc71; color: #fff; }
    .alert-error { background: #e74c3c; color: #fff; }
  </style>
</head>
<body>
  <div class="settings-wrapper">
    <h2>Configuración de usuario</h2>

    <!-- Mensajes de éxito o error -->
    <?php if($success_msg): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php elseif($error_msg): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <form action="guardar_ajustes.php" method="POST">
      <label for="full_name">Nombre completo</label>
      <input
        type="text"
        name="full_name"
        id="full_name"
        value="<?= htmlspecialchars($user['full_name'] ?? '') ?>"
        required
      >

      <label for="username">Nombre de usuario</label>
      <input
        type="text"
        id="username"
        value="<?= htmlspecialchars($user['username'] ?? '') ?>"
        disabled
      >

      <label for="currency_pref">Moneda preferida</label>
      <select name="currency_pref" id="currency_pref" required>
        <option value="S/." <?= $current_currency == 'S/.' ? 'selected' : '' ?>>S/. – Sol peruano</option>
        <option value="$" <?= $current_currency == '$' ? 'selected' : '' ?>>$ – Dólar estadounidense (USD)</option>
        <option value="€" <?= $current_currency == '€' ? 'selected' : '' ?>>€ – Euro (EUR)</option>
        <option value="$ (MXN)" <?= $current_currency == '$ (MXN)' ? 'selected' : '' ?>>$ – Peso mexicano (MXN)</option>
        <option value="$ (ARS)" <?= $current_currency == '$ (ARS)' ? 'selected' : '' ?>>$ – Peso argentino (ARS)</option>
        <option value="$ (CLP)" <?= $current_currency == '$ (CLP)' ? 'selected' : '' ?>>$ – Peso chileno (CLP)</option>
        <option value="COL$" <?= $current_currency == 'COL$' ? 'selected' : '' ?>>COL$ – Peso colombiano (COP)</option>
        <option value="VES" <?= $current_currency == 'VES' ? 'selected' : '' ?>>VES – Bolívar venezolano</option>
        <option value="$U" <?= $current_currency == '$U' ? 'selected' : '' ?>>$U – Peso uruguayo (UYU)</option>
        <option value="₲" <?= $current_currency == '₲' ? 'selected' : '' ?>>₲ – Guaraní paraguayo (PYG)</option>
        <option value="Bs" <?= $current_currency == 'Bs' ? 'selected' : '' ?>>Bs – Boliviano (BOB)</option>
        <option value="$ (EC)" <?= $current_currency == '$ (EC)' ? 'selected' : '' ?>>$ – Dólar ecuatoriano</option>
      </select>

      <button type="submit">Guardar cambios</button>
    </form>
  </div>

  <script>
    // Mantener modo oscuro si ya estaba activado
    window.onload = function(){
      if(localStorage.getItem('darkMode')==='true'){
        document.body.classList.add('dark');
      }
    }
  </script>
</body>
</html>
