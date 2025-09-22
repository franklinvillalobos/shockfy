<?php
require 'db.php';
require 'auth.php'; // protege la página

// Verificar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_SESSION['user_id'];
    $full_name = trim($_POST['full_name'] ?? '');
    $currency_pref = $_POST['currency_pref'] ?? 'S/.';

    // Validar datos mínimos
    if ($full_name === '') {
        $_SESSION['ajustes_error'] = "El nombre completo no puede estar vacío.";
        header("Location: ajustes.php");
        exit;
    }

    // Actualizar la base de datos
    $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, currency_pref = :currency_pref WHERE id = :id");
    $stmt->execute([
        ':full_name' => $full_name,
        ':currency_pref' => $currency_pref,
        ':id' => $user_id
    ]);

    // Actualizar la sesión
    $_SESSION['full_name'] = $full_name;
    $_SESSION['currency_pref'] = $currency_pref;

    // Redirigir con mensaje de éxito
    $_SESSION['ajustes_success'] = "Tus cambios se han guardado correctamente.";
    header("Location: ajustes.php");
    exit;
}

// Si se accede directamente sin POST, redirigir
header("Location: ajustes.php");
exit;
?>
