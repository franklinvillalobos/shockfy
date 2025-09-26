<?php
// send_verification.php — Genera y envía código por email (Brevo SMTP con PHPMailer)
session_start();

if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
  header('Location: login.php'); exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1) Cargar PHPMailer (tu estructura sin Composer)
require __DIR__ . '/vendor/phpmailer/src/PHPMailer.php';
require __DIR__ . '/vendor/phpmailer/src/SMTP.php';
require __DIR__ . '/vendor/phpmailer/src/Exception.php';

// 2) Conexión a BD (asume que $pdo queda disponible)
require_once __DIR__ . '/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("SET NAMES utf8mb4");

// ====== CONFIG ======
define('CODE_TTL_MIN', 15);            // validez del código (minutos)
define('DEV_MODE', false);             // true = guarda código en archivo y sesión
define('DEBUG_SMTP', false);           // true = crea mail_smtp.log con conversación SMTP

// Credenciales SMTP de Brevo:
$SMTP_HOST = 'smtp-relay.brevo.com';
$SMTP_PORT = 587; // 587 STARTTLS (recomendado) | 465 SSL
$SMTP_USERNAME = '97e746001@smtp-brevo.com'; // tu "SMTP login" de Brevo
$SMTP_PASSWORD = 'xsmtpsib-06c6f701e1b67bec653cd5be82194a4ac6eb01b45bc361247dede3e3d5f57e58-tX2rnagdjQvLyFG9';                 // tu "SMTP key" de Brevo (NO la API key web)

// Remitente (debe estar verificado en Brevo)
$FROM_EMAIL = 'info.brixpay@gmail.com';
$FROM_NAME  = 'ShockFy';

try {
  // 3) Email del usuario
  $uid = (int)$_SESSION['user_id'];
  $st = $pdo->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
  $st->execute([$uid]);
  $email = $st->fetchColumn();
  if (!$email) throw new Exception('No se encontró el email del usuario.');

  // 4) Generar código + expiración (UTC)
  $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
  $expiresUtc = (new DateTime('now', new DateTimeZone('UTC')))
                  ->modify('+' . CODE_TTL_MIN . ' minutes')
                  ->format('Y-m-d H:i:s');

  // 5) Guardar en BD
  $up = $pdo->prepare("UPDATE users 
                       SET email_verification_code = :c,
                           email_verification_expires_at = :e
                       WHERE id = :id");
  $up->execute([':c' => $code, ':e' => $expiresUtc, ':id' => $uid]);

  // 6) Enviar correo (PHPMailer + Brevo)
  $mail = new PHPMailer(true);

  // — Debug opcional a archivo (si lo necesitas)
  if (DEBUG_SMTP) {
    $mail->SMTPDebug = 2; // 0=off, 2=client+server
    $mail->Debugoutput = function($str) {
      file_put_contents(__DIR__ . '/mail_smtp.log', '['.date('c')."] {$str}\n", FILE_APPEND);
    };
  }

  $mail->isSMTP();
  $mail->Host       = $SMTP_HOST;
  $mail->SMTPAuth   = true;
  $mail->Username   = $SMTP_USERNAME; // login SMTP
  $mail->Password   = $SMTP_PASSWORD; // SMTP key
  if ((int)$SMTP_PORT === 465) {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;   // SSL puro
  } else {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS en 587
  }
  $mail->Port       = (int)$SMTP_PORT;

  $mail->CharSet = 'UTF-8';
  $mail->setFrom($FROM_EMAIL, $FROM_NAME);
  $mail->addAddress($email);

  $mail->Subject = 'Tu código de verificación – ShockFy';
  // Texto plano (simple y seguro)
  $mail->Body    = "Hola,\n\nTu código de verificación es: {$code}\nVence en " . CODE_TTL_MIN . " minutos. Si no solicitaste este email, puedes ignorar este mensaje.\n\nShockFy";


  $mail->send();

  // 7) Redirigir a tu paso 3
  header('Location: welcome.php?step=3&sent=1');
  exit;

} catch (Throwable $e) {
  // Fallback DEV (útil si el envío falla o estás probando)
  if (DEV_MODE) {
    @file_put_contents(__DIR__ . "/verification_code_user_{$uid}.txt",
      "Email: {$email}\nCode: {$code}\nExpires (UTC): {$expiresUtc}\n", LOCK_EX);
    $_SESSION['dev_last_code'] = $code; // por si lo quieres mostrar en UI
    header('Location: welcome.php?step=3&sent=1&dev=1'); exit;
  }
  $msg = urlencode('No se pudo enviar el código: ' . $e->getMessage());
  header('Location: welcome.php?step=3&err=' . $msg);
  exit;
}
