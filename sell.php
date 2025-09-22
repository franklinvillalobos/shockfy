<?php
// sell.php
require 'db.php';
require 'auth.php'; // requiere iniciar sesion
$user_id = $_SESSION['user_id'];

// traer productos
$stmt = $pdo->prepare('SELECT id, name, sale_price AS price, stock, size, color, image FROM products WHERE user_id = ? ORDER BY name');
$stmt->execute([$user_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// traer moneda seleccionada del usuario
$stmt = $pdo->prepare('SELECT currency_pref FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$currency = $stmt->fetchColumn() ?? 'S/.';

include 'header.php';
include 'sidebar.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registrar venta</title>
  <link rel="stylesheet" href="style.css">
  <style>
  label { color: #333; font-weight: 500; }
  .form-wrapper { max-width: 600px; background: #fff; padding: 20px; margin: 40px auto; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .form-wrapper h2 { margin-bottom: 20px; color: #11101d; }
  .form-wrapper label { display: block; margin-bottom: 10px; font-weight: 500; }
  .form-wrapper input, .form-wrapper select { width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
  .form-wrapper .actions { margin-top: 20px; display: flex; gap: 10px; }
  .btn { padding: 8px 16px; background: #11101D; color: #fff; text-decoration: none; border: none; border-radius: 6px; cursor: pointer; }
  .btn.cancel { background: #888; }
  .error { background: #fdd; padding: 8px; margin-bottom: 15px; border-radius: 6px; color: #900; }
  .search-wrapper { position: relative; width: 100%; margin-top: 4px; }
  #productSearch { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
  #suggestions { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #ccc; border-radius: 6px; z-index: 10; max-height: 240px; overflow-y: auto; display: none; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
  .suggest-item { padding: 8px 10px; cursor: pointer; display: flex; justify-content: space-between; gap: 12px; align-items: center; }
  .suggest-item small { color: #666; display: block; }
  .suggest-item:hover { background: #f0f8ff; }
  .suggest-right { text-align: right; min-width:90px; }
  .stock-badge { font-size: 12px; color: #888; }
  </style>
</head>
<body>

<section class="home-section">
  <div class="form-wrapper">
    <h2>Registrar venta</h2>

    <?php if(!empty($_GET['error'])): ?>
      <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <form id="saleForm" action="process_sale.php" method="post" onsubmit="return validateForm()">

      <!-- PRODUCTO (search bar con sugerencias) -->
      <label for="productSearch">Producto:
        <div class="search-wrapper" style="width: 100%; box-sizing: border-box; text-align: left;">
          <input type="text" id="productSearch" placeholder="Escribe nombre, talla, color o código..." autocomplete="off" style="width:100%; box-sizing: border-box;" />
          <div id="suggestions" role="listbox" aria-label="Sugerencias"></div>
        </div>
      </label>
      <input type="hidden" id="selected_product_id" name="product_id">

      <!-- CANTIDAD -->
      <label for="quantity">Cantidad:
        <input type="number" id="quantity" name="quantity" value="1" min="1" required>
      </label>

      <!-- PRECIO DE VENTA -->
      <label for="unit_price">Precio de venta:
        <div style="position: relative;">
          <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color:#555; font-weight:500;">
            <?= htmlspecialchars($currency) ?>
          </span>
          <input id="unit_price" name="unit_price" type="number" step="0.01" required style="width:100%; box-sizing:border-box; padding-left: 40px;">
        </div>
      </label>

      <div class="actions">
        <button type="submit" class="btn">Registrar venta</button>
        <a href="index.php" class="btn cancel">Cancelar</a>
      </div>
    </form>
  </div>
</section>

<script>
const products = <?= json_encode($products, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
const searchInput = document.getElementById('productSearch');
const suggestions = document.getElementById('suggestions');
const selectedProductId = document.getElementById('selected_product_id');
const unitPriceInput = document.getElementById('unit_price');
const quantityInput = document.getElementById('quantity');

let filtered = [];
let activeIndex = -1;

function escapeHtml(str){
  return str ? String(str)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#039;') : '';
}

// Miniaturas de productos en sugerencias
function showSuggestions(list){
  if(!list.length){
    suggestions.style.display = 'none';
    suggestions.innerHTML = '';
    return;
  }
  suggestions.style.display = 'block';
  suggestions.innerHTML = '';
  list.forEach((p, idx) => {
    const div = document.createElement('div');
    div.className = 'suggest-item';
    div.dataset.index = idx;
    div.innerHTML = `
      <div style="display:flex; align-items:center; gap:8px;">
        ${p.image ? `<img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}" style="width:40px; height:40px; object-fit:cover; border-radius:6px;">` : ''}
        <div>
          <strong>${escapeHtml(p.name)}</strong>
          <small>${escapeHtml((p.size ? 'Talla: '+p.size+' | ' : '') + (p.color ? 'Color: '+p.color : ''))}</small>
        </div>
      </div>
      <div class="suggest-right">
        <div><?= htmlspecialchars($currency) ?> ${Number(p.price).toFixed(2)}</div>
        <div class="stock-badge">${p.stock} uds</div>
      </div>
    `;
    div.addEventListener('click', () => chooseProduct(p));
    suggestions.appendChild(div);
  });
  activeIndex = -1;
}

function filterProducts(){
  const term = searchInput.value.trim().toLowerCase();
  if(term===''){ filtered = products.slice(0,8); }
  else {
    const words = term.split(/\s+/).filter(Boolean);
    filtered = products.filter(p => {
      const txt = `${p.name} ${p.size||''} ${p.color||''} ${p.id||''}`.toLowerCase();
      return words.every(w=>txt.includes(w));
    }).slice(0,40);
  }
  showSuggestions(filtered);
}

function chooseProduct(p){
  selectedProductId.value = p.id;
  unitPriceInput.value = Number(p.price).toFixed(2);
  searchInput.value = p.name + (p.size?' - '+p.size:'') + (p.color?' - '+p.color:'');
  suggestions.style.display='none';
  if(p.stock==0) alert('Atención: el producto seleccionado tiene stock 0.');
}

// Debounce
function debounce(fn, delay=120){
  let t;
  return function(...args){ clearTimeout(t); t=setTimeout(()=>fn.apply(this,args), delay); }
}
const debouncedFilter = debounce(filterProducts,120);
searchInput.addEventListener('input',debouncedFilter);

// Keyboard navigation
searchInput.addEventListener('keydown', function(e){
  const items = suggestions.querySelectorAll('.suggest-item');
  if(items.length===0) return;
  if(e.key==='ArrowDown'){ e.preventDefault(); activeIndex=Math.min(activeIndex+1, items.length-1); highlight(items,activeIndex); }
  else if(e.key==='ArrowUp'){ e.preventDefault(); activeIndex=Math.max(activeIndex-1,0); highlight(items,activeIndex); }
  else if(e.key==='Enter'){ e.preventDefault(); if(activeIndex>=0 && items[activeIndex]) items[activeIndex].click(); else if(items.length===1) items[0].click(); }
  else if(e.key==='Escape'){ suggestions.style.display='none'; }
});

function highlight(items, idx){
  items.forEach((it,i)=>{ it.style.background=i===idx?'#f0f8ff':''; if(i===idx) it.scrollIntoView({block:'nearest'}); });
}

document.addEventListener('click', e=>{
  if(!document.querySelector('.search-wrapper').contains(e.target)){
    suggestions.style.display='none';
  }
});

function validateForm(){
  if(!selectedProductId.value){ alert('Selecciona un producto antes de registrar la venta.'); searchInput.focus(); return false; }
  if(Number(quantityInput.value)<=0){ alert('Ingrese una cantidad válida.'); quantityInput.focus(); return false; }
  return true;
}

window.addEventListener('load', ()=>{ filterProducts(); });
</script>

<script>
  // Aplicar modo oscuro si está activado en localStorage
  window.addEventListener('load', () => {
    if(localStorage.getItem('darkMode') === 'true'){
      document.body.classList.add('dark');
    }
  });
</script>

<!-- Modal para vista previa de imagen -->
<div id="imageModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.8); justify-content:center; align-items:center;">
  <span id="closeModal" style="position:absolute; top:20px; right:30px; color:white; font-size:30px; cursor:pointer;">&times;</span>
  <img id="modalImg" src="" style="max-width:90%; max-height:90%; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.5);">
</div>

</body>
</html>
