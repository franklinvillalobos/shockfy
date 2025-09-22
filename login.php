<?php
session_start();
require 'db.php';

include 'header.php';

$error = '';
$account_disabled = false;
$disabled_user_name = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if($user['status'] == 0){
            // Usuario desactivado
            $account_disabled = true;
            $disabled_user_name = $user['full_name'];
        } else {
            // Usuario activo, iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            header('Location: index.php');
            exit;
        }
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login - StockFy</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif;}
html,body{height:100%;}

/* Fondo gradiente azul + blanco suave */
body{
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg,#4742e3,#4c57e9);
    overflow:hidden;
}

/* Caja login */
.login-box{
    background:#f7f7f7;
    padding:50px 35px 35px 35px;
    border-radius:14px;
    box-shadow:0 12px 30px rgba(0,0,0,.2);
    width:100%;
    max-width:400px;
    text-align:center;
    position:relative;
    color:#1a1a2e;
    transform:translateY(-50px);
    opacity:0;
    animation:fadeSlide 0.8s forwards;
}

/* Animación de entrada */
@keyframes fadeSlide{
    to{transform:translateY(0);opacity:1;}
}

/* Logo animado */
.login-box .logo{
    width:80px;
    height:80px;
    margin:0 auto 20px auto;
    background: linear-gradient(135deg,#4742e3,#4c57e9);
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:32px;
    font-weight:bold;
    color:#fff;
    animation:logoBounce 1s infinite alternate;
}
@keyframes logoBounce{
    0%{transform:translateY(0);}
    100%{transform:translateY(-10px);}
}

/* Título */
.login-box h2{
    margin-bottom:30px;
    font-size:26px;
    font-weight:700;
    color:#1a1a2e;
}

/* Input Group */
.input-group{
    position:relative;
    margin-bottom:20px;
}
.input-group input{
    width:100%;
    padding:12px 45px 12px 15px;
    border-radius:8px;
    border:none;
    font-size:15px;
    outline:none;
    background:#e8e8e8;
    color:#1a1a2e;
    transition:0.3s;
}
.input-group input:focus{
    background:#fff;
    box-shadow:0 0 8px #4742e3;
    border:1px solid #4742e3;
}
.input-group i{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#888;
    font-size:18px;
    transition:0.3s, transform 0.3s;
}
.input-group input:focus + i{
    color:#4742e3;
    transform:translateY(-5px);
}

/* Botón */
.login-box button{
    width:100%;
    padding:12px;
    background:linear-gradient(135deg,#4742e3,#4c57e9);
    border:none;
    border-radius:8px;
    color:#fff;
    font-size:16px;
    cursor:pointer;
    transition:0.4s;
    box-shadow:0 4px 15px rgba(0,0,0,.2);
}
.login-box button:hover{
    background:linear-gradient(135deg,#3b36c8,#3f47e0);
    box-shadow:0 6px 20px rgba(0,0,0,.3);
}

/* Links */
.login-box .forgot{
    display:block;
    margin-top:15px;
    font-size:14px;
    color:#4742e3;
    text-decoration:none;
}
.login-box .forgot:hover{text-decoration:underline;}

/* Toast */
.toast{
    position:absolute;
    top:-60px;
    left:50%;
    transform:translateX(-50%);
    background:#e74c3c;
    color:#fff;
    padding:10px 20px;
    border-radius:8px;
    box-shadow:0 4px 10px rgba(0,0,0,.2);
    display:none;
    font-size:14px;
    font-weight:500;
    z-index:100;
    transition:0.3s;
}

/* Modal Usuario Desactivado */
.modal { display:none; position:fixed; z-index:10000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);}
.modal-content { background:#fff; margin:100px auto; padding:30px; border-radius:12px; width:400px; position:relative; box-shadow:0 4px 20px rgba(0,0,0,.3); text-align:center;}
.modal-content h3 { margin-top:0; color:#333;}
.modal-content p { color:#555; margin:10px 0;}
.modal-content .close { color:#aaa; position:absolute; right:15px; top:10px; font-size:28px; font-weight:bold; cursor:pointer;}
.modal-content .close:hover { color:#000; }

/* Responsive */
@media(max-width:450px){
    .login-box{padding:40px 20px;}
    .login-box .logo{width:60px;height:60px;font-size:24px;}
}
</style>
</head>
<body>

<div class="login-box">
    <div class="logo">💼</div>
    <h2>StockFy</h2>

    <!-- Toast para errores -->
    <?php if($error): ?>
        <div class="toast" id="errorToast"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" id="loginForm">
        <div class="input-group">
            <input type="text" name="username" placeholder="Usuario" required>
            <i class="fas fa-user"></i>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Contraseña" id="passwordField" required>
            <i class="fas fa-eye" id="togglePassword"></i>
        </div>
        <button type="submit">Ingresar</button>
        <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>
    </form>
</div>

<!-- Modal Usuario Desactivado -->
<?php if($account_disabled): ?>
<div id="disabledModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeDisabledModal()">&times;</span>
    <h3>Cuenta Desactivada</h3>
    <p>Hola <?php echo htmlspecialchars($disabled_user_name); ?>, tu cuenta ha sido desactivada y no puedes iniciar sesión.</p>
    <p>Por favor contacta al soporte para reactivar tu cuenta.</p>
  </div>
</div>
<?php endif; ?>

<script>
// Toast de error
window.onload = function(){
    // Mostrar toast
    let toast = document.getElementById('errorToast');
    if(toast){
        toast.style.display='block';
        setTimeout(()=>{toast.style.display='none';},3500);
    }

    // Mostrar modal desactivado si corresponde
    <?php if($account_disabled): ?>
    var modal = document.getElementById('disabledModal');
    if(modal){
        modal.style.display = 'block';
    }
    <?php endif; ?>
};

// Mostrar/ocultar contraseña
const togglePassword = document.getElementById('togglePassword');
const passwordField = document.getElementById('passwordField');
togglePassword.addEventListener('click',()=>{
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type',type);
    togglePassword.classList.toggle('fa-eye-slash');
});

function closeDisabledModal(){
    document.getElementById('disabledModal').style.display='none';
}
</script>

</body>
</html>
