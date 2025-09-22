<?php
require 'db.php';
require 'auth.php'; // requiere iniciar sesion

include 'header.php';
$id = intval($_GET['id'] ?? 0);
$product = $pdo->prepare("SELECT * FROM products WHERE id=?");
$product->execute([$id]);
$p = $product->fetch();

if(!$p){ exit("Producto no encontrado"); }

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST'){
    $name  = trim($_POST['name']);
    $size  = trim($_POST['size']);
    $color = trim($_POST['color']);
    $cost  = floatval($_POST['cost_price']);
    $sale  = floatval($_POST['sale_price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);

    $errors = [];
    if($name === '') $errors[] = "El nombre es obligatorio.";
    if($category_id === 0) $errors[] = "Debe seleccionar una categoría.";

    if(isset($_FILES['image']) && $_FILES['image']['name'] !== ''){
        $allowed_ext = ['jpg','jpeg','png','gif'];
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if(!in_array($file_ext, $allowed_ext)){
            $errors[] = "Formato de imagen no permitido.";
        } else {
            $uploadDir = 'uploads/';
            if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $new_name = $uploadDir . uniqid('prod_', true) . '.' . $file_ext;
            if(!move_uploaded_file($_FILES['image']['tmp_name'], $new_name)){
                $errors[] = "Error al subir la imagen.";
            } else {
                if($p['image'] && file_exists($p['image'])) unlink($p['image']);
                $image_path = $new_name;
            }
        }
    } else {
        $image_path = $p['image'];
    }

    if(empty($errors)){
        $stmt = $pdo->prepare("UPDATE products SET name=?, size=?, color=?, cost_price=?, sale_price=?, stock=?, category_id=?, image=? WHERE id=?");
        $stmt->execute([$name,$size,$color,$cost,$sale,$stock,$category_id,$image_path,$id]);
        header("Location: products.php?msg=Producto+actualizado");
        exit;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Editar producto</title>
    <link rel="stylesheet" href="style.css">
    <style>
/* Misma apariencia que add_product.php */
label { color: #333; font-weight: 500; }

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
    justify-content: center;
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
            <h2>Editar producto</h2>

            <?php if(!empty($errors)): ?>
                <div class="error">
                    <?php foreach($errors as $e) echo "<p>$e</p>"; ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <label>Nombre de la prenda:
                    <input name="name" value="<?= htmlspecialchars($p['name']) ?>" required>
                </label>

                <label>Talla:
                    <input name="size" value="<?= htmlspecialchars($p['size']) ?>">
                </label>

                <label>Color:
                    <input name="color" value="<?= htmlspecialchars($p['color']) ?>">
                </label>

                <label>Precio de costo:
                    <input name="cost_price" type="number" step="0.01" value="<?= $p['cost_price'] ?>" required>
                </label>

                <label>Precio de venta:
                    <input name="sale_price" type="number" step="0.01" value="<?= $p['sale_price'] ?>" required>
                </label>

                <label>Stock:
                    <input name="stock" type="number" value="<?= $p['stock'] ?>" required>
                </label>

                <label>Categoría:
                    <select name="category_id" required>
                        <option value="">-- Seleccione categoría --</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $p['category_id']==$cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>Imagen del producto:
                    <input type="file" name="image" accept="image/*">
                </label>
                <?php if($p['image'] && file_exists($p['image'])): ?>
                    <img src="<?= $p['image'] ?>" alt="Imagen actual" style="margin-top:10px; max-width:150px; border-radius:6px;">
                <?php endif; ?>

                <div class="actions">
                    <button type="submit" class="btn">Guardar</button>
                    <a href="products.php" class="btn cancel">Cancelar</a>
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
