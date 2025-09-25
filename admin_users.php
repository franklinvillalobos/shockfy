<?php
require 'db.php';
require 'auth.php';



// Verificar que el usuario logueado sea admin
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || $user['role'] != 'admin') {
    die("Acceso denegado. Solo administradores.");
}

// --- Procesar agregado usuario AJAX ---
if(isset($_POST['ajax_add_user'])){
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $response = ['status'=>'success','msg'=>'Usuario agregado correctamente.','new_id'=>0];

    if(strlen($password)<6){
        $response = ['status'=>'error','msg'=>'La contraseña debe tener al menos 6 caracteres.'];
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username=?");
        $stmt->execute([$username]);
        if($stmt->rowCount()>0){
            $response = ['status'=>'error','msg'=>'El usuario ya existe.'];
        } else {
            $passwordHash = password_hash($password,PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, role, status) VALUES (?, ?, ?, ?, 1)");
            if($stmt->execute([$full_name,$username,$passwordHash,$role])){
                $response['new_id'] = $pdo->lastInsertId();
            } else {
                $response = ['status'=>'error','msg'=>'Error al agregar el usuario.'];
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// --- Editar usuario AJAX ---
if(isset($_POST['ajax_edit_user'])){
    $edit_id = intval($_POST['edit_id']);
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    $response = ['status'=>'success','msg'=>'Usuario actualizado correctamente.'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username=? AND id<>?");
    $stmt->execute([$username, $edit_id]);
    if ($stmt->rowCount() > 0){
        $response = ['status'=>'error','msg'=>'El nombre de usuario ya está en uso.'];
    } elseif($edit_id == $user_id && $role != 'admin'){
        $response = ['status'=>'error','msg'=>'No puedes cambiar tu propio rol de administrador.'];
    } elseif($password && strlen($password)<6){
        $response = ['status'=>'error','msg'=>'La contraseña debe tener al menos 6 caracteres.'];
    } else {
        if($password){
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET full_name=?, username=?, role=?, password=? WHERE id=?");
            $stmt->execute([$full_name,$username,$role,$passwordHash,$edit_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name=?, username=?, role=? WHERE id=?");
            $stmt->execute([$full_name,$username,$role,$edit_id]);
        }

        // Registrar actividad
        $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
        $stmt->execute([$user_id, "Editó usuario ID $edit_id"]);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// --- Activar/Desactivar usuario AJAX ---
if(isset($_POST['ajax_toggle_user'])){
    $toggle_id = intval($_POST['toggle_id']);
    $stmt = $pdo->prepare("SELECT status FROM users WHERE id=?");
    $stmt->execute([$toggle_id]);
    $u = $stmt->fetch();
    if(!$u || $toggle_id == $user_id){
        $response = ['status'=>'error','msg'=>'Operación no permitida.'];
    } else {
        $new_status = $u['status']==1 ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE users SET status=? WHERE id=?");
        $stmt->execute([$new_status, $toggle_id]);
        $response = ['status'=>'success','msg'=>"Usuario ".($new_status? "activado":"desactivado").".", 'status_value'=>$new_status];

        // Registrar actividad
        $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
        $stmt->execute([$user_id, "Cambió estado usuario ID $toggle_id a $new_status"]);
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// --- Eliminar usuario AJAX ---
if(isset($_POST['ajax_delete_user'])){
    $delete_id = intval($_POST['delete_id']);
    $response = ['status'=>'success','msg'=>'Usuario eliminado correctamente.'];
    if($delete_id == $user_id){
        $response = ['status'=>'error','msg'=>'No puedes eliminar tu propio usuario.'];
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$delete_id]);
        $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
        $stmt->execute([$user_id, "Eliminó usuario ID $delete_id"]);
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// --- Buscar y filtrar ---
$filter = [];
$where = " WHERE 1=1 ";
if(isset($_GET['role']) && $_GET['role'] != '') { $where .= " AND role=?"; $filter[]=$_GET['role']; }
if(isset($_GET['status']) && $_GET['status'] != '') { $where .= " AND status=?"; $filter[]=$_GET['status']; }
if(isset($_GET['search']) && $_GET['search'] != '') { $where .= " AND (full_name LIKE ? OR username LIKE ?)"; $filter[]="%".$_GET['search']."%"; $filter[]="%".$_GET['search']."%"; }

$stmt = $pdo->prepare("SELECT id, full_name, username, role, status FROM users $where ORDER BY id DESC");
$stmt->execute($filter);
$users = $stmt->fetchAll();

// --- Obtener logs de actividad
$logs_stmt = $pdo->query("SELECT ul.*, u.username FROM user_logs ul JOIN users u ON ul.user_id=u.id ORDER BY ul.id DESC LIMIT 50");
$logs = $logs_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Usuarios Avanzado</title>
<link rel="stylesheet" href="style.css">
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f4f6f8; }
.container { max-width: 1100px; margin:40px auto; padding:25px; background:#fff; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,.1);}
h2 { margin-bottom:20px; color:#333; }
table { width:100%; border-collapse:collapse; margin-top:20px;}
table th, table td { padding:12px; border-bottom:1px solid #e0e0e0; text-align:left; font-size:15px; }
table th { background:#f7f7f7; font-weight:600; }
table tr:hover { background:#f1f1f1; transition:0.2s; }
.btn-edit, .btn-delete, .btn-toggle { padding:6px 12px; border:none; border-radius:6px; font-size:14px; cursor:pointer; transition:0.3s; margin-right:5px; }
.btn-edit { background:#3498db;color:#fff; } .btn-edit:hover{ background:#2980b9; }
.btn-delete { background:#e74c3c;color:#fff; } .btn-delete:hover{ background:#c0392b; }
.btn-toggle { background:#f39c12;color:#fff; } .btn-toggle:hover{ background:#d35400; }

.modal { display:none; position:fixed; z-index:10000; padding-top:100px; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);}
.modal-content { background:#fff; margin:auto; padding:20px; border-radius:12px; width:450px; position:relative; box-shadow:0 4px 20px rgba(0,0,0,.3);}
.modal-content h3 { margin-top:0; color:#333; }
.close { color:#aaa; position:absolute; right:15px; top:10px; font-size:28px; font-weight:bold; cursor:pointer;}
.close:hover { color:#000; }
.toast { position: fixed; top:20px; right:20px; background:#4CAF50; color:#fff; padding:15px 25px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,.3); z-index:9999; display:none; font-weight:500; animation: fadein 0.3s;}
.toast.error { background:#e74c3c; }
@keyframes fadein { from {opacity:0; top:0px;} to {opacity:1; top:20px;} }

.filter-form input, .filter-form select { padding:8px; margin-right:10px; border-radius:6px; border:1px solid #ccc; }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="container">

<h2>Usuarios</h2>
<button onclick="openAddModal()" style="margin-bottom:15px; padding:10px 20px; border:none; border-radius:6px; background:#2ecc71; color:#fff; cursor:pointer;">Agregar Usuario</button>

<form class="filter-form" style="margin-bottom:20px;">
    <input type="text" name="search" placeholder="Buscar por nombre o usuario" value="<?php echo $_GET['search']??''; ?>">
    <select name="role">
        <option value="">Todos los roles</option>
        <option value="admin" <?php if(($_GET['role']??'')=='admin') echo 'selected';?>>Administrador</option>
        <option value="user" <?php if(($_GET['role']??'')=='user') echo 'selected';?>>Usuario</option>
    </select>
    <select name="status">
        <option value="">Todos los estados</option>
        <option value="1" <?php if(($_GET['status']??'')=='1') echo 'selected';?>>Activo</option>
        <option value="0" <?php if(($_GET['status']??'')=='0') echo 'selected';?>>Desactivado</option>
    </select>
    <button type="submit">Filtrar</button>
</form>

<table id="usersTable">
<tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
<?php foreach($users as $u): ?>
<tr data-id="<?php echo $u['id']; ?>">
<td><?php echo $u['id']; ?></td>
<td><?php echo htmlspecialchars($u['full_name']); ?></td>
<td><?php echo htmlspecialchars($u['username']); ?></td>
<td><?php echo $u['role']; ?></td>
<td><?php echo $u['status']==1 ? 'Activo' : 'Desactivado'; ?></td>
<td>
    <?php if($u['id'] != $user_id): ?>
    <button class="btn-edit" onclick="openEditModal(<?php echo $u['id']; ?>,'<?php echo htmlspecialchars($u['full_name'],ENT_QUOTES); ?>','<?php echo htmlspecialchars($u['username'],ENT_QUOTES); ?>','<?php echo $u['role']; ?>')">Editar</button>
    <button class="btn-toggle" onclick="toggleUser(<?php echo $u['id']; ?>,this)"><?php echo $u['status']==1?'Desactivar':'Activar';?></button>
    <button class="btn-delete" onclick="deleteUser(<?php echo $u['id']; ?>)">Eliminar</button>
    <?php else: ?> - <?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>

<h2>Historial de Actividad</h2>
<table>
<tr><th>ID</th><th>Usuario</th><th>Acción</th><th>Fecha</th></tr>
<?php foreach($logs as $l): ?>
<tr>
<td><?php echo $l['id']; ?></td>
<td><?php echo htmlspecialchars($l['username']); ?></td>
<td><?php echo htmlspecialchars($l['action']); ?></td>
<td><?php echo $l['created_at']; ?></td>
</tr>
<?php endforeach; ?>
</table>

</div>

<!-- Toast -->
<div id="toast" class="toast"></div>

<!-- Modal Add/Edit -->
<div id="userModal" class="modal">
<div class="modal-content">
<span class="close" onclick="closeModal()">&times;</span>
<h3 id="modalTitle">Agregar Usuario</h3>
<form id="userForm">
<input type="hidden" name="edit_id" id="edit_id">
<label>Nombre completo:</label>
<input type="text" name="full_name" id="full_name" required>
<label>Usuario:</label>
<input type="text" name="username" id="username" required>
<label>Contraseña:</label>
<input type="password" name="password" id="password">
<label>Rol:</label>
<select name="role" id="role">
<option value="user">Usuario</option>
<option value="admin">Administrador</option>
</select>
<button type="submit" id="modalSubmit">Guardar</button>
</form>
</div>
</div>

<script>
function showToast(msg,status='success'){
    let toast = document.getElementById('toast');
    toast.className = 'toast';
    if(status==='error') toast.classList.add('error');
    toast.innerText = msg;
    toast.style.display='block';
    setTimeout(()=>{ toast.style.display='none'; },3000);
}

// Modal
let modal = document.getElementById('userModal');
function openAddModal(){
    modal.style.display='block';
    document.getElementById('modalTitle').innerText='Agregar Usuario';
    document.getElementById('modalSubmit').innerText='Agregar';
    document.getElementById('userForm').reset();
    document.getElementById('edit_id').value='';
}
function openEditModal(id,name,username,role){
    modal.style.display='block';
    document.getElementById('modalTitle').innerText='Editar Usuario';
    document.getElementById('modalSubmit').innerText='Guardar';
    document.getElementById('edit_id').value=id;
    document.getElementById('full_name').value=name;
    document.getElementById('username').value=username;
    document.getElementById('role').value=role;
    document.getElementById('password').value='';
}
function closeModal(){ modal.style.display='none'; }
window.onclick=function(e){ if(e.target==modal) modal.style.display='none'; }

// Form submit
document.getElementById('userForm').addEventListener('submit',function(e){
    e.preventDefault();
    let formData = new FormData(this);
    let url = formData.get('edit_id') ? 'admin_users.php?ajax_edit_user=1' : 'admin_users.php?ajax_add_user=1';
    fetch(url,{method:'POST',body:formData})
    .then(res=>res.json())
    .then(data=>{
        showToast(data.msg,data.status);
        if(data.status=='success') location.reload();
    });
});

// Delete
function deleteUser(id){
    if(!confirm('¿Seguro que quieres eliminar este usuario?')) return;
    let formData = new FormData(); formData.append('ajax_delete_user',1); formData.append('delete_id',id);
    fetch('admin_users.php',{method:'POST',body:formData})
    .then(res=>res.json())
    .then(data=>{
        showToast(data.msg,data.status);
        if(data.status=='success') location.reload();
    });
}

// Toggle active/inactive
function toggleUser(id,btn){
    let formData = new FormData(); formData.append('ajax_toggle_user',1); formData.append('toggle_id',id);
    fetch('admin_users.php',{method:'POST',body:formData})
    .then(res=>res.json())
    .then(data=>{
        showToast(data.msg,data.status);
        if(data.status=='success'){
            btn.innerText = data.status_value==1 ? 'Desactivar' : 'Activar';
            let statusCell = btn.parentElement.parentElement.children[4];
            statusCell.innerText = data.status_value==1 ? 'Activo':'Desactivado';
        }
    });
}


// Form submit
document.getElementById('userForm').addEventListener('submit',function(e){
    e.preventDefault();
    let formData = new FormData(this);

    // Agregamos este campo para que PHP detecte si es agregar o editar
    if(document.getElementById('edit_id').value){
        formData.append('ajax_edit_user',1); // ya es edición
    } else {
        formData.append('ajax_add_user',1); // agregar usuario
    }

    fetch('admin_users.php',{
        method:'POST',
        body:formData
    })
    .then(res=>res.json())
    .then(data=>{
        showToast(data.msg,data.status);
        if(data.status=='success') location.reload();
    });
});

</script>
</body>
</html>
