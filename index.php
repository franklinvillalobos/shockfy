<?php
require 'db.php';
require 'auth.php'; // requiere iniciar sesion

include 'header.php';

// Traer la moneda elegida por el usuario
$stmt = $pdo->prepare('SELECT currency_pref FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$currency = $stmt->fetchColumn() ?: 'S/.';

// últimos 3 productos
$stmt = $pdo->prepare('SELECT * FROM products WHERE user_id = :user_id ORDER BY id DESC LIMIT 3');
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$products = $stmt->fetchAll();

// ventas recientes
$stmt = $pdo->prepare('
    SELECT s.*, p.name AS product_name, p.size, p.color
    FROM sales s 
    JOIN products p ON s.product_id = p.id
    WHERE s.user_id = :user_id
    ORDER BY s.sale_date DESC 
    LIMIT 10
');
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$recentSales = $stmt->fetchAll();

// total ventas del mes actual
$stmt = $pdo->prepare('
    SELECT COALESCE(SUM(total),0) as total_mes 
    FROM sales 
    WHERE YEAR(sale_date) = YEAR(CURDATE()) 
      AND MONTH(sale_date) = MONTH(CURDATE())
      AND user_id = :user_id
');
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$totalMes = $stmt->fetchColumn();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>ShockFy</title>
<link rel="stylesheet" href="style.css">
<style>
/* ==================== ULTRA DELUXE DASHBOARD ==================== */
body.home { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f7; color: #11101D; transition: background 0.3s, color 0.3s; }
body.dark { background: #12122b; color: #e0e0e0; }

/* BOTÓN DE TEMA */
#darkToggle { position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg,#6a11cb,#2575fc); color: white; padding: 10px 20px; border-radius: 20px; font-weight: 700; cursor: pointer; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.2); transition: transform 0.3s, box-shadow 0.3s; }
#darkToggle:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.3); }

/* CONTAINER */
.container { max-width: 1250px; margin: 100px auto; padding: 20px; }

/* TITULOS */
h1 { text-align: center; font-size: 2.8em; font-weight: 700; margin-bottom: 50px; }

/* MENU PREMIUM */
.menu { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-bottom: 50px; }
.menu a { background: linear-gradient(135deg,#6a11cb,#2575fc); color: white; padding: 14px 28px; border-radius: 20px; font-weight: 700; text-decoration: none; cursor: pointer; transition: transform 0.3s, box-shadow 0.3s; border: none; }
.menu a:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.3); }

/* STATS CARDS */
.stats { display: flex; flex-wrap: wrap; justify-content: center; gap: 35px; margin-bottom: 60px; }
.card { flex: 1 1 280px; min-width: 280px; background: linear-gradient(135deg,#4e38de,#365bef); color: white; border-radius: 25px; padding: 35px 30px; text-align: center; box-shadow: 0 12px 30px rgba(0,0,0,0.25); transition: transform 0.3s, box-shadow 0.3s; position: relative; overflow: hidden; }
.card:hover { transform: translateY(-10px); box-shadow: 0 18px 40px rgba(0,0,0,0.35); }
.card h3 { font-size: 2.2em; margin-bottom: 10px; }
.card p { font-size: 1em; opacity: 0.9; }
.card::after { content: ''; position: absolute; width: 250%; height: 250%; background: rgba(255,255,255,0.06); top: -50%; left: -50%; transform: rotate(45deg); pointer-events: none; }
body.dark .card { background: linear-gradient(135deg,#4527a0,#1e88e5); }

/* ================== TABLAS PREMIUM ================== */
table { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 20px; overflow: hidden; margin-bottom: 50px; box-shadow: 0 12px 25px rgba(0,0,0,0.15); font-size: 0.95em; }
thead th { padding: 16px; font-weight: 700; text-align: left; }
tbody td { padding: 14px; font-weight: 500; transition: background 0.3s; position: relative; }
.low-stock { color: #ff4c4c; font-weight: bold; }
.delete-btn { background: linear-gradient(135deg,#e74c3c,#ff4c4c); color: #fff !important; padding: 7px 16px; border-radius: 12px; font-weight: 600; font-size: 0.9em; text-decoration: none; display: inline-block; transition: transform 0.2s, box-shadow 0.3s; }
.delete-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }

/* Últimos productos agregados */
.products-table thead { background: #fa4c07ff !important; color: #070707ff !important; }
.products-table tbody tr:nth-child(odd) td { background: #c2b9b9ff !important; }
.products-table tbody tr:nth-child(even) td { background: #c2b9b9ff !important; }
.products-table tbody tr:hover td { background: #a0b0ff !important; }

/* Ventas recientes */
.sales-table thead { background: #2575fc !important; color: #070707ff !important; }
.sales-table tbody tr:nth-child(odd) td { background: #c2b9b9ff !important; }
.sales-table tbody tr:nth-child(even) td { background: #c2b9b9ff !important; }
.sales-table tbody tr:hover td { background: #99c0ff !important; }

/* Dark mode tablas */
body.dark .products-table thead { background: #4073ffff !important; }
body.dark .products-table tbody tr:nth-child(odd) td { background: #1b0d41ff !important; }
body.dark .products-table tbody tr:nth-child(even) td { background: #1b0d41ff !important; }
body.dark .products-table tbody tr:hover td { background: #1b0d41ff !important; }

body.dark .sales-table thead { background: #c2b9b9ff !important; }
body.dark .sales-table tbody tr:nth-child(odd) td { background: #1b0d41ff !important; }
body.dark .sales-table tbody tr:nth-child(even) td { background: #1b0d41ff !important; }
body.dark .sales-table tbody tr:hover td { background: #1b0d41ff !important; }

/* NOTIFICACION */
.notification { position: fixed; bottom: 5cm; left: 50%; transform: translateX(-50%); background: #2ecc71; color: white; padding: 14px 28px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); font-weight: bold; z-index: 9999; opacity: 0; transition: opacity 0.4s, transform 0.4s; }
.notification.show { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
</head>
<body class="home">

<?php include 'sidebar.php'; ?>

<button id="darkToggle">🌙</button>

<div class="container">
  <h1>Sistema de Ventas</h1>

  <div class="stats">
    <div class="card">
      <h3><?= $currency . ' ' . number_format($totalMes,2) ?></h3>
      <p>Ventas del mes actual</p>
    </div>
  </div>

  <div class="menu">
    <a href="add_product.php">➕ Agregar producto</a>
    <a href="sell.php">💸 Registrar venta</a>
    <a href="sales_report.php">📊 Reporte</a>
    <a href="products.php">👕 Productos</a>
  </div>

  <h2>Últimos productos agregados</h2>
  <table class="products-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Código</th>
          <th>Prenda</th>
          <th>Talla</th>
          <th>Color</th>
          <th>Precio</th>
          <th>Venta</th>
          <th>Stock</th>
          <th>Creado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($products as $p): ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['code']) ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['size']) ?></td>
            <td><?= htmlspecialchars($p['color']) ?></td>
            <td><?= $currency . ' ' . number_format($p['cost_price'],2) ?></td>
            <td><?= $currency . ' ' . number_format($p['sale_price'],2) ?></td>
            <td>
              <?php if($p['stock'] < 5): ?>
                <span class="low-stock">⚠ <?= $p['stock'] ?></span>
              <?php else: ?>
                <?= $p['stock'] ?>
              <?php endif; ?>
            </td>
            <td><?= $p['created_at'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
  </table>

  <h2>Ventas recientes</h2>
  <table class="sales-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Producto</th>
        <th>Talla</th>
        <th>Color</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Total</th>
        <th>Fecha</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($recentSales as $s): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td><?= htmlspecialchars($s['product_name']) ?></td>
          <td><?= htmlspecialchars($s['size']) ?></td>
          <td><?= htmlspecialchars($s['color']) ?></td>
          <td><?= $s['quantity'] ?></td>
          <td><?= $currency . ' ' . number_format($s['unit_price'],2) ?></td>
          <td><?= $currency . ' ' . number_format($s['total'],2) ?></td>
          <td><?= $s['sale_date'] ?></td>
          <td>
            <a href="delete_sale.php?id=<?= $s['id'] ?>" class="delete-btn"
               onclick="return confirm('¿Seguro que deseas eliminar esta venta?');">
              Eliminar
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
const btn = document.getElementById('darkToggle');
const body = document.body;

if(localStorage.getItem('darkMode') === 'true'){
  body.classList.add('dark');
  btn.textContent = '☀️';
}

btn.addEventListener('click', () => {
  body.classList.toggle('dark');
  const dark = body.classList.contains('dark');
  btn.textContent = dark ? '☀️' : '🌙 ';
  localStorage.setItem('darkMode', dark);
});

function showNotification(message, duration = 3000) {
  const notif = document.getElementById('notification');
  notif.textContent = message;
  notif.style.display = 'block';
  notif.classList.add('show');
  setTimeout(() => {
    notif.classList.remove('show');
    setTimeout(() => notif.style.display = 'none', 400);
  }, duration);
}

<?php if(!empty($_GET['msg'])): ?>
showNotification("<?= addslashes($_GET['msg']) ?>");
<?php endif; ?>
</script>
</body>
</html>
