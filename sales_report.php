<?php
require 'db.php';
require 'auth.php'; // requiere iniciar sesion
$user_id = $_SESSION['user_id']; // usuario logueado

include 'header.php';

// ==== MONEDA DEL USUARIO ====
$stmt = $pdo->prepare("SELECT currency_pref FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$currency = $stmt->fetchColumn() ?: 'S/.';

// Ventas del mes actual
$year  = intval(date('Y'));
$month = intval(date('n'));

$query = "SELECT s.*, p.name, p.cost_price
          FROM sales s
          JOIN products p ON s.product_id = p.id
          WHERE s.user_id = ? AND YEAR(s.sale_date)=? AND MONTH(s.sale_date)=?
          ORDER BY s.sale_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id, $year, $month]);
$sales = $stmt->fetchAll();

// ==== RESUMEN DEL MES ====
$stmt = $pdo->prepare("
    SELECT 
        COALESCE(SUM(s.total),0) AS totalVentasMes,
        COUNT(*) AS numVentas,
        COALESCE(SUM(s.total - (p.cost_price*s.quantity)),0) AS gananciaMes
    FROM sales s
    JOIN products p ON s.product_id = p.id
    WHERE s.user_id = ? AND YEAR(s.sale_date)=? AND MONTH(s.sale_date)=?
");
$stmt->execute([$user_id, $year, $month]);
$resumen = $stmt->fetch();

$totalVentasMes = $resumen['totalVentasMes'];
$numVentas = $resumen['numVentas'];
$gananciaMes = $resumen['gananciaMes'];

// ==== INVENTARIO ====
$inv = $pdo->prepare("SELECT SUM(stock) AS cant, SUM(stock*cost_price) AS valor FROM products WHERE user_id = ?");
$inv->execute([$user_id]);
$inv = $inv->fetch();

$invCantidad = $inv['cant'] ?? 0;
$invValor = $inv['valor'] ?? 0;

// ==== INGRESO TOTAL ====
$stmt = $pdo->prepare("SELECT COALESCE(SUM(total),0) FROM sales WHERE user_id = ?");
$stmt->execute([$user_id]);
$ingresoTotal = $stmt->fetchColumn();

// ==== GRAFICO ====
$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$ventasPorDia = array_fill(1, $days, 0);
foreach($sales as $s){
    $d = (int)date('j', strtotime($s['sale_date']));
    $ventasPorDia[$d] += $s['total'];
}

// ==== RANKING ====
$stmt = $pdo->prepare("SELECT p.name, SUM(s.quantity) q, SUM(s.total) t
                       FROM sales s
                       JOIN products p ON s.product_id = p.id
                       WHERE YEAR(s.sale_date)=? AND MONTH(s.sale_date)=? AND s.user_id = ?
                       GROUP BY p.id ORDER BY q DESC");
$stmt->execute([$year, $month, $user_id]);
$ranking = $stmt->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Reporte de ventas</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {
  background: #f0f2f7;
  color: #11101D;
  transition: background .3s, color .3s;
}
body.dark {
  background: #12122b;
  color: #e0e0e0;
}
.container { max-width:1100px; margin:60px auto; padding:20px; }
.summary { display:flex; flex-wrap:wrap; gap:15px; justify-content:center; margin-bottom:30px; }

/* === Cards Ultra Pro === */
.card {
    background: linear-gradient(135deg,#6a11cb,#2575fc);
    color: #fff;
    padding: 25px;
    border-radius: 16px;
    min-width: 200px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    transition: transform 0.4s, box-shadow 0.4s, background 0.4s;
}
.card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 15px 35px rgba(0,0,0,0.25);
    background: linear-gradient(135deg,#2575fc,#6a11cb);
}

/* === Color único para todas las cards (con prioridad) === */
.card {
    background: linear-gradient(135deg, #7b2ff7, #1c92d2) !important;
}

.card:hover {
    background: linear-gradient(135deg, #1c92d2, #7b2ff7) !important;
}
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="container">
  <h2 style="text-align:center; margin-bottom:40px;">Reporte de ventas</h2>

  <div class="summary">
    <div class="card ventas-mes"><h3><?= $currency . ' ' . number_format($totalVentasMes,2) ?></h3><p>Total ventas mes</p></div>
    <div class="card num-ventas"><h3><?=$numVentas?></h3><p>Número de ventas</p></div>
    <div class="card ganancia"><h3><?= $currency . ' ' . number_format($gananciaMes,2) ?></h3><p>Saldo / Ganancia</p></div>
    <div class="card inventario"><h3><?=$invCantidad?></h3><p>Prendas en inventario</p></div>
    <div class="card valor-inventario"><h3><?= $currency . ' ' . number_format($invValor,2) ?></h3><p>Valor del inventario</p></div>
    <div class="card ingreso-total"><h3><?= $currency . ' ' . number_format($ingresoTotal,2) ?></h3><p>Ingreso total acumulado</p></div>
  </div>

  <canvas id="ventasChart"></canvas>

  <h3 style="margin-top:40px;">Ranking de productos más vendidos</h3>
  <table>
    <thead>
      <tr><th>Producto</th><th>Cantidad</th><th>Total (<?= $currency ?>)</th></tr>
    </thead>
    <tbody>
      <?php foreach($ranking as $r): ?>
        <tr>
          <td><?=htmlspecialchars($r['name'])?></td>
          <td><?=$r['q']?></td>
          <td><?= $currency . ' ' . number_format($r['t'],2) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$ranking): ?>
        <tr><td colspan="3">No hay ventas para este período.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
if(localStorage.getItem('darkMode') === 'true'){
    document.body.classList.add('dark');
}
new Chart(document.getElementById('ventasChart').getContext('2d'), {
  type:'bar',
  data:{
    labels: <?=json_encode(array_keys($ventasPorDia))?>,
    datasets:[{
      label:'Ventas por día (<?= $currency ?>)',
      data: <?=json_encode(array_values($ventasPorDia))?>,
      backgroundColor:'rgba(106,17,203,0.7)'
    }]
  },
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
</script>
</body>
</html>
