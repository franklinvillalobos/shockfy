<?php
// categories.php
require 'db.php';
require 'auth.php'; // requiere iniciar sesion
$user_id = $_SESSION['user_id']; // ID del usuario logueado

include 'header.php';

// Procesar creación o edición de categoría desde POST via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $id = $_POST['id'] ?? null;

    if ($name !== '') {
        if ($id) {
            // Editar categoría solo si pertenece al usuario
            $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$name, $id, $user_id]);
            echo json_encode(['status'=>'ok','action'=>'edit','id'=>$id,'name'=>$name]);
        } else {
            // Insertar nueva categoría asignada al usuario
            $stmt = $pdo->prepare("INSERT INTO categories (name, user_id) VALUES (?, ?)");
            $stmt->execute([$name, $user_id]);
            $id = $pdo->lastInsertId();
            echo json_encode(['status'=>'ok','action'=>'add','id'=>$id,'name'=>$name]);
        }
    } else {
        echo json_encode(['status'=>'error','message'=>'El nombre no puede estar vacío']);
    }
    exit;
}

// Eliminar categoría vía GET (solo si pertenece al usuario)
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete'], $user_id]);
    header("Location: categories.php");
    exit;
}

// Obtener todas las categorías del usuario logueado
$categories = $pdo->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY name");
$categories->execute([$user_id]);
$categories = $categories->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Categorías</title>
<link rel="stylesheet" href="style.css">
<style>

#toastMessage {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #11101D;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    z-index: 2000;
    font-weight: bold;
}
#toastMessage.show {
    opacity: 1;
    pointer-events: auto;
}


.form-wrapper {
    max-width: 700px;
    margin: 40px auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.1);
}
.form-wrapper h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #11101d;
}
#addNewBtn {
    display: inline-block;
    margin-bottom: 15px;
    padding: 6px 12px;
    background: #3498db;
    color: #fff;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}
#categoriesTable {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.05);
    border-radius: 12px;
    overflow: hidden;
}
#categoriesTable th, #categoriesTable td { padding: 12px 15px; text-align: left; }
#categoriesTable th { background: #3498db; color: #fff; font-weight: 500; }
#categoriesTable tr:nth-child(even){ background: #f9f9f9; }
#categoriesTable tr:hover{ background: #eaf4fc; }
.actions-cell a { margin-right: 8px; padding: 4px 10px; border-radius: 6px; text-decoration: none; font-size: 14px; }
.actions-cell .edit { background: #4cafef; color: #fff; }
.actions-cell .delete { background: #e74c3c; color: #fff; }

/* Modal flotante */
#categoryModal {
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
#categoryModal.active { display: flex; animation: fadeIn 0.3s ease; }
@keyframes fadeIn { from {opacity:0;} to{opacity:1;} }

.modal-content {
    background: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    max-width: 400px;
    width: 90%;
    position: relative;
}
.modal-content h3 { margin-bottom: 15px; color: #11101d; text-align: center; }
.modal-content input { width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 15px; }
.modal-content button { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; }
.modal-content .btn.cancel { background: #888; color: #fff; margin-left: 10px; }
.modal-close {
    position: absolute;
    top: 12px;
    right: 15px;
    font-size: 22px;
    cursor: pointer;
    color: #333;
}
</style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="form-wrapper">
    <h2>Categorías</h2>

    <div id="addNewBtn">+ Agregar categoría</div>

    <table id="categoriesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="categoryBody">
            <?php foreach($categories as $cat): ?>
                <tr id="cat-<?= $cat['id'] ?>">
                    <td><?= $cat['id'] ?></td>
                    <td class="cat-name"><?= htmlspecialchars($cat['name']) ?></td>
                    <td class="actions-cell">
                        <a href="#" class="edit" onclick="openModal(<?= $cat['id'] ?>,'<?= htmlspecialchars(addslashes($cat['name'])) ?>')">Editar</a>
                        <a href="?delete=<?= $cat['id'] ?>" class="delete" onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal flotante -->
<div id="categoryModal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle">Nueva categoría</h3>
        <input type="hidden" id="category_id">
        <input type="text" id="category_name" placeholder="Nombre de la categoría">
        <div style="text-align:center;">
            <button class="btn" onclick="saveCategory()">Guardar</button>
            <button class="btn cancel" onclick="closeModal()">Cancelar</button>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById('categoryModal');
const catIdInput = document.getElementById('category_id');
const catNameInput = document.getElementById('category_name');
const modalTitle = document.getElementById('modalTitle');
const categoryBody = document.getElementById('categoryBody');

document.getElementById('addNewBtn').onclick = () => {
    modalTitle.textContent = 'Nueva categoría';
    catIdInput.value = '';
    catNameInput.value = '';
    modal.classList.add('active');
    catNameInput.focus();
};

function openModal(id,name){
    modalTitle.textContent = 'Editar categoría';
    catIdInput.value = id;
    catNameInput.value = name;
    modal.classList.add('active');
    catNameInput.focus();
}

function closeModal(){
    modal.classList.remove('active');
    catIdInput.value = '';
    catNameInput.value = '';
}

// Guardar categoría vía AJAX
function saveCategory(){
    const name = catNameInput.value.trim();
    if(!name){ alert('Ingresa un nombre'); return; }
    const id = catIdInput.value;

    const formData = new FormData();
    formData.append('name', name);
    if(id) formData.append('id', id);

    fetch('categories.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if(data.status === 'ok'){
                if(data.action === 'add'){
                    const tr = document.createElement('tr');
                    tr.id = 'cat-'+data.id;
                    tr.innerHTML = `<td>${data.id}</td>
                        <td class="cat-name">${data.name}</td>
                        <td class="actions-cell">
                            <a href="#" class="edit" onclick="openModal(${data.id},'${data.name.replace(/'/g,"\\'")}')">Editar</a>
                            <a href="?delete=${data.id}" class="delete" onclick="return confirmDelete(${data.id})">Eliminar</a>
                        </td>`;
                    categoryBody.appendChild(tr);
                    showToast('Categoría creada');
                } else if(data.action === 'edit'){
                    const tr = document.getElementById('cat-'+data.id);
                    tr.querySelector('.cat-name').textContent = data.name;
                    showToast('Categoría editada');
                }
                closeModal();
            } else alert(data.message);
        });
}

// Cerrar modal al hacer clic fuera del contenido
modal.addEventListener('click', e => {
    if(e.target === modal) closeModal();
});

// Confirmar y eliminar categoría con mensaje
function confirmDelete(id){
    if(confirm('¿Eliminar esta categoría?')){
        fetch(`categories.php?delete=${id}`)
            .then(() => {
                const tr = document.getElementById('cat-'+id);
                if(tr) tr.remove();
                showToast('Categoría eliminada');
            });
    }
    return false; // evitar navegación normal
}

// Toast
function showToast(msg){
    const toast = document.getElementById('toastMessage');
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(()=>toast.classList.remove('show'),2000);
}

</script>

<div id="toastMessage"></div>
</body>
</html>
