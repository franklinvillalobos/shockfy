<?php
// add_product.php
require 'db.php';
require 'auth.php'; // requiere iniciar sesion
$user_id = $_SESSION['user_id']; // el ID del usuario que está logueado

include 'header.php';


// Traer todas las categorías
$categories = $pdo->prepare("SELECT id, name FROM categories WHERE user_id = ? ORDER BY name");
$categories->execute([$user_id]);
$categories = $categories->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code  = trim($_POST['code'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $size  = trim($_POST['size'] ?? '');
    $color = trim($_POST['color'] ?? '');
    $cost  = floatval($_POST['cost_price'] ?? 0);
    $sale  = floatval($_POST['sale_price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);

    if ($name === '') {
        header('Location: add_product.php?error=El nombre es obligatorio');
        exit;
    }
    if ($category_id === 0) {
        header('Location: add_product.php?error=Debe seleccionar una categoría');
        exit;
    }

    // Procesar imagen si se sube
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $originalName = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array($ext, $allowed)) {
            header('Location: add_product.php?error=Formato de imagen no permitido');
            exit;
        }

        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newName = uniqid('prod_', true) . '.' . $ext;
        $imagePath = $uploadDir . $newName;

        move_uploaded_file($tmpName, $imagePath);
    }

    // INSERT del producto incluyendo la imagen y el usuario logueado
$sql = 'INSERT INTO products (code, name, size, color, cost_price, sale_price, stock, category_id, image, user_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$code ?: null, $name, $size, $color, $cost, $sale, $stock, $category_id, $imagePath, $user_id]);
    header('Location: index.php?msg=Producto agregado correctamente');
    exit;
} catch (Exception $e) {
    header('Location: add_product.php?error=' . urlencode($e->getMessage()));
    exit;
}

}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Agregar producto</title>
    <link rel="stylesheet" href="style.css">
    <style>
/* Cambiar el color de todos los labels */
label {
  color: #333;
  font-weight: 500;
}

.form-wrapper{
    max-width: 600px;
    background: #fff;
    padding: 20px;
    margin: 40px auto;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
}
.form-wrapper h2{
    margin-bottom: 20px;
    color: #11101d;
}
.form-wrapper label{
    display: block;
    margin-bottom: 10px;
    font-weight: 500;
}
.form-wrapper input,
.form-wrapper select{
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-top: 4px;
}
.form-wrapper .actions{
    margin-top: 20px;
    display: flex;
    gap: 10px;
}
.btn{
    padding: 8px 16px;
    background: #11101D;
    color: #fff;
    text-decoration: none;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.btn.cancel{
    background: #888;
}
.error{
    background: #fdd;
    padding: 8px;
    margin-bottom: 15px;
    border-radius: 6px;
    color: #900;
}
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    

    <section class="home-section">
        <div class="form-wrapper">
            <h2>Agregar producto</h2>

            <?php if(!empty($_GET['error'])): ?>
                <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
            <?php endif; ?>

          <form method="post" enctype="multipart/form-data">
                <label>Código (opcional):
                    <input name="code">
                </label>

                <label>Nombre de la prenda:
                    <input name="name" required>
                </label>

                <label>Talla:
                    <input name="size">
                </label>

                <label>Color:
                    <input name="color">
                </label>

                <label>Precio de costo:
                    <input name="cost_price" type="number" step="0.01" value="0.00" required>
                </label>

                <label>Precio de venta:
                    <input name="sale_price" type="number" step="0.01" value="0.00" required>
                </label>

                <label>Stock inicial:
                    <input name="stock" type="number" value="0" required>
                </label>

                <label>Categoría:
                    <select name="category_id" required>
                        <option value="">-- Seleccione categoría --</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>Imagen del producto:
                    <input type="file" name="image" accept="image/*">
                </label>

                <div class="actions">
                    <button type="submit" class="btn">Guardar</button>
                    <a href="index.php" class="btn cancel">Cancelar</a>
                </div>
            </form>
        </div>
    </section>

    <script>
        window.onload = function(){
            if(localStorage.getItem('darkMode') === 'true'){
                document.body.classList.add('dark');
            }
        }
    </script>
</body>
</html>
