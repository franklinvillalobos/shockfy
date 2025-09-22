<?php
require 'db.php';
require 'auth.php';
include 'header.php';

// Obtener la preferencia de moneda del usuario
$stmt = $pdo->prepare("SELECT currency_pref FROM users WHERE id=?");
$stmt->execute([$user['id']]);
$currencyPref = $stmt->fetchColumn() ?: 'S/.';

// Lista de símbolos de moneda (opcionalmente puedes usarla para mostrar el símbolo correspondiente)
$currencySymbols = [
  'S/.' => 'S/.',
  '$' => '$',
  'USD' => '$',
  'EUR' => '€',
  'VES' => 'Bs.',
  'COP' => '$',
  'CLP' => '$',
  'MXN' => '$',
  'ARS' => '$'
];

$currencySymbol = $currencySymbols[$currencyPref] ?? $currencyPref;

$categories = $pdo->prepare("SELECT id, name FROM categories WHERE user_id=? ORDER BY name");
$categories->execute([$user['id']]);
$categories = $categories->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT p.*,c.name AS category_name
      FROM products p
      LEFT JOIN categories c ON p.category_id=c.id
      WHERE p.user_id=? ORDER BY p.name";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id']]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Productos</title>
  <style>
    /* -- NO se toca el estilo -- */
    body {
      font-family: 'Segoe UI', Roboto, Arial, sans-serif;
      background: #f4f6f9;
      color: #333;
      padding: 40px;
    }

    body.dark {
      background: #181818;
      color: #f1f1f1
    }

    .container {
      max-width: 1400px;
      margin: 60px auto;
      padding: 30px;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, .05);
    }

    body.dark .container {
      background: #242424
    }

    h1 {
      text-align: center;
      margin-bottom: 25px;
      font-weight: 600
    }

    .search-box,
    .order-box,
    .category-box {
      display: flex;
      justify-content: center;
      gap: 10px;
      flex-wrap: wrap;
      margin: 10px 0;
    }

    .search-box input,
    .order-box select,
    .category-box select {
      padding: 7px 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    body.dark input,
    body.dark select {
      background: #333;
      border: 1px solid #666;
      color: #fff
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
      gap: 24px;
      margin-top: 25px;
    }

    .product-card {
      background: #fff;
      border-radius: 14px;
      padding: 16px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, .06);
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      transition: transform .2s, box-shadow .2s, background .3s;
    }

    body.dark .product-card {
      background: #303030
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
    }

    .product-img {
      width: 140px;
      height: 110px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 12px;
      transition: transform .3s;
    }

    .product-card:hover .product-img {
      transform: scale(1.05)
    }

    .product-name {
      font-size: 15px;
      font-weight: 600;
      margin-bottom: 4px
    }

    .badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 500;
      margin-top: 4px;
    }

    .badge.cat {
      background: #3498db20;
      color: #3498db
    }

    .stock {
      font-weight: 600;
      margin-top: 6px;
    }

    .stock.ok {
      color: #2e7d32
    }

    .stock.mid {
      color: #f9a825
    }

    .stock.low {
      color: #c62828
    }

    .product-actions {
      margin-top: 12px
    }

    .product-actions a {
      font-size: 13px;
      padding: 6px 12px;
      margin: 2px;
      border-radius: 6px;
      text-decoration: none;
      display: inline-block;
    }

    a.edit {
      background: #3498db;
      color: #fff
    }

    a.edit:hover {
      background: #2980b9
    }

    a.delete {
      background: #e74c3c;
      color: #fff
    }

    a.delete:hover {
      background: #c0392b
    }

    .fab {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 60px;
      height: 60px;
      background: #3498db;
      color: #fff;
      font-size: 34px;
      line-height: 60px;
      text-align: center;
      border-radius: 50%;
      box-shadow: 0 4px 10px rgba(0, 0, 0, .25);
      text-decoration: none;
      transition: background .3s, transform .2s;
    }

    .fab:hover {
      background: #2980b9;
      transform: scale(1.08);
    }
  </style>
</head>

<body>
  <?php include 'sidebar.php'; ?>
  <div class="container">
    <h1>Inventario de productos</h1>

    <div class="search-box">
      <input type="text" id="search" placeholder="Buscar producto...">
    </div>
    <div class="category-box">
      <select id="category">
        <option value="">Todas las categorías</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat['name'] ?? '') ?>"><?= htmlspecialchars($cat['name'] ?? '') ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="order-box">
      <select id="order">
        <option value="name">Ordenar por: Nombre</option>
        <option value="stock_desc">Mayor stock</option>
        <option value="stock_asc">Menor stock</option>
      </select>
    </div>

    <div id="productsBody" class="products-grid">
      <?php foreach ($products as $p):
        $stockClass = $p['stock'] < 5 ? 'low' : ($p['stock'] < 10 ? 'mid' : 'ok'); ?>
        <div class="product-card" data-name="<?= strtolower($p['name']) ?>"
          data-cat="<?= htmlspecialchars($p['category_name'] ?? '') ?>"
          data-stock="<?= $p['stock'] ?>">
          <?php if (!empty($p['image'])): ?>
            <img class="product-img" src="<?= htmlspecialchars($p['image'] ?? '') ?>" alt="<?= htmlspecialchars($p['name'] ?? '') ?>">
          <?php endif; ?>
          <div class="product-name"><?= htmlspecialchars($p['name'] ?? '') ?></div>
          <div class="badge cat"><?= htmlspecialchars($p['category_name'] ?? '-') ?></div>
          <div class="stock <?= $stockClass ?>">Stock: <?= $p['stock'] ?></div>
          <small style="opacity:.8">
            Código <?= htmlspecialchars($p['code'] ?? '') ?> |
            <?= htmlspecialchars($p['color'] ?? '') ?> <?= htmlspecialchars($p['size'] ?? '') ?>
          </small>
          <div style="margin-top:6px;font-size:13px;opacity:.85">
            <?= htmlspecialchars($currencySymbol ?? '') ?> <?= number_format($p['sale_price'], 2) ?>
          </div>
          <div class="product-actions">
            <a class="edit" href="edit_product.php?id=<?= $p['id'] ?>">Editar</a>
            <a class="delete" href="delete_product.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <a href="add_product.php" class="fab" title="Agregar producto">+</a>

  <!-- Modal -->
  <div id="imageModal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,.8);justify-content:center;align-items:center;">
    <span id="closeModal" style="position:absolute;top:20px;right:30px;color:#fff;font-size:30px;cursor:pointer">&times;</span>
    <img id="modalImg" style="max-width:90%;max-height:90%;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.5)">
  </div>

  <script>
    const cards = [...document.querySelectorAll('.product-card')],
      search = document.getElementById('search'),
      order = document.getElementById('order'),
      cat = document.getElementById('category');

    function render() {
      let t = search.value.toLowerCase(),
        c = cat.value;
      let f = cards.filter(x => x.dataset.name.includes(t) && (!c || x.dataset.cat === c));
      if (order.value === 'stock_desc') f.sort((a, b) => b.dataset.stock - a.dataset.stock);
      else if (order.value === 'stock_asc') f.sort((a, b) => a.dataset.stock - b.dataset.stock);
      else f.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
      const body = document.getElementById('productsBody');
      body.innerHTML = '';
      f.forEach(e => body.appendChild(e));
    }
    search.addEventListener('input', render);
    order.addEventListener('change', render);
    cat.addEventListener('change', render);

    const modal = document.getElementById('imageModal'),
      img = document.getElementById('modalImg'),
      close = document.getElementById('closeModal');
    document.querySelectorAll('.product-img').forEach(i => i.addEventListener('click', () => {
      modal.style.display = 'flex';
      img.src = i.src;
    }));
    document.getElementById('closeModal').addEventListener('click', () => modal.style.display = 'none');
    modal.addEventListener('click', e => {
      if (e.target === modal) modal.style.display = 'none';
    });
  </script>
</body>

</html>