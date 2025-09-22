<?php
// process_sale.php
require 'db.php';
require 'auth.php'; // requiere iniciar sesión

// Obtener id del usuario logueado
$user_id = $_SESSION['user_id'] ?? 0;
if($user_id <= 0){
    header('Location: sell.php?error=Usuario no identificado');
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: index.php');
    exit;
}

$product_id = intval($_POST['product_id'] ?? 0);
$quantity   = intval($_POST['quantity'] ?? 0);
$unit_price = floatval($_POST['unit_price'] ?? 0);

if($product_id <= 0 || $quantity <= 0){
    header('Location: sell.php?error=Datos inválidos');
    exit;
}

try {
    // iniciar transacción
    $pdo->beginTransaction();

    // bloquear fila del producto
    $stmt = $pdo->prepare('SELECT stock, sale_price FROM products WHERE id = ? FOR UPDATE');
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if(!$product){
        $pdo->rollBack();
        header('Location: sell.php?error=Producto no encontrado');
        exit;
    }

    if($product['stock'] < $quantity){
        $pdo->rollBack();
        header('Location: sell.php?error=Stock insuficiente (hay ' . $product['stock'] . ' unidades)');
        exit;
    }

    // actualizar stock
    $stmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
    $stmt->execute([$quantity, $product_id]);

    // calcular total
    $total = round($quantity * $unit_price, 2);

    // insertar venta con user_id
    $stmt = $pdo->prepare('INSERT INTO sales (product_id, user_id, quantity, unit_price, total) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$product_id, $user_id, $quantity, $unit_price, $total]);

    $pdo->commit();
    header('Location: index.php?msg=Venta registrada correctamente');
    exit;

} catch (Exception $e){
    if($pdo->inTransaction()) $pdo->rollBack();
    exit('Error al procesar la venta: ' . $e->getMessage());
}
