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
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="assets/img/favicon.png" type="image/png">
<link rel="shortcut icon" href="assets/img/favicon.png" type="image/png">
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root{
  --bg:#e9eef5;         /* fondo contrastado */
  --panel:#ffffff;
  --panel-2:#f2f5f9;
  --text:#0f172a;
  --muted:#64748b;
  --primary:#2563eb;
  --primary-2:#60a5fa;
  --success:#16a34a;
  --danger:#dc2626;
  --warning:#d97706;
  --border:#e2e8f0;
  --shadow:0 10px 24px rgba(15,23,42,.06);
  --radius:16px;
}

*{box-sizing:border-box}
body{background:var(--bg); color:var(--text); margin:0; font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
.container{
  max-width:1200px; margin:24px auto 64px; padding:16px;
  background:var(--panel); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow);
}
.header{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px}
.header .title{display:flex; align-items:center; gap:12px}
.header .icon{
  width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,#e0edff,#f1f7ff);
  display:grid; place-items:center; border:1px solid #dbeafe; box-shadow:var(--shadow)
}
.header h2{margin:0; font-size:24px; font-weight:800; color:#0b1220}
.sub{font-size:12px; color:var(--muted); margin-top:2px}

.toolbar{display:flex; gap:8px; align-items:center; flex-wrap:wrap}
.pill{
  display:flex; align-items:center; gap:8px; background:#fff; border:1px solid var(--border);
  padding:8px 10px; border-radius:12px; box-shadow:var(--shadow);
}
.pill input{border:none; outline:none; background:transparent; color:inherit; min-width:200px}
.btn{
  padding:10px 14px; border-radius:12px; border:1px solid var(--border);
  background:var(--panel-2); color:var(--text); font-weight:800; cursor:pointer; box-shadow:var(--shadow);
}
.btn:hover{ transform:translateY(-1px); background:#e8edf4; border-color:#b8c3d4}
.btn.primary{ background:linear-gradient(135deg,var(--primary),var(--primary-2)); color:#fff; border:none}
.btn.ghost{ background:var(--panel-2); color:var(--text); border-color:#cfd7e3}
.actions{display:flex; gap:8px; align-items:center}

/* GRID de cards (NO tocamos su HTML interno) */
.summary{
  display:grid; grid-template-columns:repeat(3, minmax(220px,1fr)); gap:14px; margin:12px 0 18px;
}

/* === Cards (respetando tu gradient y estructura) === */
.card{
  background: linear-gradient(135deg, #7b2ff7, #1c92d2) !important;
  color:#fff;
  padding:22px;
  border-radius:16px;
  min-width: 200px;
  text-align:center;
  box-shadow:0 10px 25px rgba(0,0,0,0.15);
  transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
}
.card:hover{
  transform: translateY(-6px);
  box-shadow: 0 15px 35px rgba(0,0,0,0.22);
  background: linear-gradient(135deg, #1c92d2, #7b2ff7) !important;
}
.card h3{margin:0 0 6px; font-size:22px; font-weight:800}
.card p{margin:0; font-size:12px; opacity:.95}

/* Sección gráfico + tarjeta contenedora */
.section{
  margin-top:14px; background:var(--panel); border:1px solid var(--border);
  border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden;
}
.section-header{
  padding:14px 16px; border-bottom:1px solid var(--border);
  background:linear-gradient(180deg,#ffffff,#f7fafc);
  display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
}
.section-title{ font-size:14px; font-weight:800 }
.section-hint{ font-size:12px; color:var(--muted) }
.section-body{ padding:16px }

/* Tabla ranking */
table{ width:100%; border-collapse:separate; border-spacing:0 }
thead th{
  font-size:12px; text-transform:uppercase; letter-spacing:.06em; color:#475569;
  padding:14px 16px; background:#f8fafc; border-bottom:1px solid var(--border); text-align:left
}
tbody td{ padding:14px 16px; border-bottom:1px solid var(--border) }
tbody tr{ transition: background .18s ease }
tbody tr:hover{ background:#f1f5f9 }

/* Modo oscuro */
body.dark{ background:#0c1326; color:#e5e7eb }
body.dark .container{ background:#0b1220; border-color:#1f2a4a }
body.dark .section, body.dark .pill{ background:#0b1220; border-color:#1f2a4a }
body.dark .section-header{ background:#0e1630 }
body.dark thead th{ background:#0e1630; color:#a5b4fc; border-color:#1f2a4a }
body.dark tbody td{ border-color:#1f2a4a }
body.dark tbody tr:hover{ background:rgba(99,102,241,.08) }
body.dark .btn{ background:#0e1630; border-color:#2a365a; color:#e5e7eb }
body.dark .btn:hover{ background:#132146; border-color:#33416b }

/* Print-friendly */
@media print{
  .toolbar, .actions, .header .icon, .sidebar, #sidebar { display:none !important; }
  .container{ box-shadow:none; border:none; }
}
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="container">
  <div class="header">
    <div class="title">
      <div class="icon">
        <!-- SVG report -->
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
          <path d="M5 3h10l4 4v14H5z" stroke="#2563eb" stroke-width="2"/>
          <path d="M15 3v5h5" stroke="#60a5fa" stroke-width="2"/>
          <path d="M8 13h8M8 17h5" stroke="#2563eb" stroke-width="2"/>
        </svg>
      </div>
      <div>
        <h2>Reporte de ventas</h2>
        <div class="sub">Resumen del mes actual, ventas por día y ranking de productos.</div>
      </div>
    </div>
    <div class="actions toolbar">
      <div class="pill">
      
      </div>
      <button class="btn ghost" id="btnExport">Exportar CSV</button>
      <button class="btn primary" onclick="window.print()">Imprimir</button>
    </div>
  </div>

  <!-- GRID de cards: NO modificamos su HTML interno -->
  <div class="summary">
    <div class="card ventas-mes"><h3><?= $currency . ' ' . number_format($totalVentasMes,2) ?></h3><p>Total ventas mes</p></div>
    <div class="card num-ventas"><h3><?=$numVentas?></h3><p>Número de ventas</p></div>
    <div class="card ganancia"><h3><?= $currency . ' ' . number_format($gananciaMes,2) ?></h3><p>Saldo / Ganancia</p></div>
    <div class="card inventario"><h3><?=$invCantidad?></h3><p>Prendas en inventario</p></div>
    <div class="card valor-inventario"><h3><?= $currency . ' ' . number_format($invValor,2) ?></h3><p>Valor del inventario</p></div>
    <div class="card ingreso-total"><h3><?= $currency . ' ' . number_format($ingresoTotal,2) ?></h3><p>Ingreso total acumulado</p></div>
  </div>

  <!-- Gráfico -->
  <div class="section">
    <div class="section-header">
      <div class="section-title">Ventas por día (<?= htmlspecialchars($currency) ?>)</div>
      <div class="section-hint">Mes actual: <?= date('F Y') ?></div>
    </div>
    <div class="section-body">
      <canvas id="ventasChart" height="120"></canvas>
    </div>
  </div>

  <!-- Ranking -->
  <div class="section" style="margin-top:14px;">
    <div class="section-header">
      <div class="section-title">Ranking de productos más vendidos</div>
      <div class="section-hint"><?= $ranking ? 'Ordenado por cantidad vendida' : 'No hay ventas para este período' ?></div>
    </div>
    <div class="section-body">
      <table id="rankingTable">
        <thead>
          <tr><th>Producto</th><th>Cantidad</th><th>Total (<?= $currency ?>)</th></tr>
        </thead>
        <tbody id="rankingBody">
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
  </div>
</div>

<script>
  // Dark mode coherente con el resto
  if(localStorage.getItem('darkMode') === 'true'){
    document.body.classList.add('dark');
  }

  // Chart
  const ctx = document.getElementById('ventasChart').getContext('2d');
  const ventasChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?=json_encode(array_keys($ventasPorDia))?>,
      datasets: [{
        label: 'Ventas por día (<?= $currency ?>)',
        data: <?=json_encode(array_values($ventasPorDia))?>,
        backgroundColor: 'rgba(37, 99, 235, 0.75)',          // azul consistente
        borderColor: 'rgba(37, 99, 235, 1)',
        borderWidth: 1,
        hoverBackgroundColor: 'rgba(96, 165, 250, 0.85)'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { mode: 'index', intersect: false }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,.25)' } },
        x: { grid: { display: false } }
      }
    }
  });

  // Buscar en ranking
  const rankSearch = document.getElementById('rankSearch');
  const rankingBody = document.getElementById('rankingBody');
  rankSearch?.addEventListener('input', () => {
    const q = rankSearch.value.trim().toLowerCase();
    [...rankingBody.rows].forEach(tr => {
      const name = tr.cells[0]?.textContent.toLowerCase() || '';
      tr.style.display = (!q || name.includes(q)) ? '' : 'none';
    });
  });

  // Exportar CSV (de la tabla de ranking tal cual se ve)
  document.getElementById('btnExport')?.addEventListener('click', () => {
    const rows = [['Producto','Cantidad','Total (<?= $currency ?>)']];
    [...rankingBody.rows].forEach(tr => {
      if (tr.style.display === 'none') return; // solo lo visible según filtro
      const cols = [...tr.cells].map(td => td.textContent.replace(/\s+/g,' ').trim());
      if (cols.length === 3) rows.push(cols);
    });
    const csv = rows.map(r => r.map(v => `"${v.replace(/"/g,'""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = 'ranking_ventas_mes.csv';
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    URL.revokeObjectURL(url);
  });
</script>
</body>
</html>
