<?php
require 'db.php';
require 'auth.php';

// Obtener usuario
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT full_name, username, currency_pref, timezone, time_format FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// Defaults seguros
$current_currency = $user['currency_pref'] ?? 'S/.';
$current_tz       = $user['timezone']      ?? 'America/New_York'; // por defecto USA (NY)
$current_fmt      = $user['time_format']   ?? '12h';              // por defecto 12h

// Mensajes flash
$success_msg = $_SESSION['ajustes_success'] ?? '';
$error_msg   = $_SESSION['ajustes_error']   ?? '';
unset($_SESSION['ajustes_success'], $_SESSION['ajustes_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/png">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ajustes</title>
  <link rel="stylesheet" href="style.css">
  <style>
    :root {
      --sidebar-w: 260px;
      --bg: #f5f7fb;
      --card: #ffffff;
      --text: #0f172a;
      --muted: #6b7280;
      --primary: #299EE6;
      --primary-600:#1c7fc0;
      --ring: rgba(41,158,230,.35);
    }

    body { background: var(--bg); color: var(--text); }
    body.dark { background:#1f2125; color:#e9edf5; }

    .app-shell{
      display:flex;
      min-height:100vh;
      transition: background .3s;
    }

    .content{
      flex:1;
      padding:24px;
    }

    /* Si tu sidebar es fixed en tu CSS global, deja esta clase */
    .with-fixed-sidebar{
      margin-left: var(--sidebar-w);
    }

    .page-title{
      font-size: 28px;
      font-weight: 700;
      margin: 6px 0 22px 0;
      letter-spacing:.2px;
    }

    .settings-grid{
      display:grid;
      grid-template-columns: 1fr;
      gap: 20px;
      max-width: 980px;
      margin: 0 auto;
    }

    .card{
      background: var(--card);
      border-radius: 14px;
      box-shadow: 0 8px 24px rgba(15,23,42,.06);
      padding: 22px;
      transition: background .3s, color .3s, box-shadow .3s;
    }
    body.dark .card{ background:#262a33; box-shadow: 0 10px 28px rgba(0,0,0,.35); }

    .card h3{
      font-size: 18px;
      margin-bottom: 14px;
    }

    label{
      display:block;
      margin: 10px 0 8px;
      font-weight:600;
    }

    .help{ color: var(--muted); font-size: 13px; margin-top: 4px; }

    input[type="text"], select{
      width:100%;
      padding:12px 14px;
      border:1px solid #d1d5db;
      border-radius:10px;
      outline:none;
      font-size:15px;
      background:#fff;
      transition: border .2s, box-shadow .2s, background .3s, color .3s;
    }
    input[type="text"]:focus, select:focus{
      border-color: var(--primary);
      box-shadow: 0 0 0 4px var(--ring);
    }

    body.dark input[type="text"], body.dark select{ background:#323644; color:#e9edf5; border-color:#475569; }
    body.dark input[type="text"]:focus, body.dark select:focus{ box-shadow: 0 0 0 4px rgba(41,158,230,.25); }

    .row{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:14px;
    }
    @media (max-width: 680px){ .row{ grid-template-columns: 1fr; } }

    .btn{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:11px 16px;
      border-radius:10px;
      border: none;
      background: var(--primary);
      color:#fff;
      font-weight:600;
      cursor:pointer;
      transition: background .2s, transform .05s;
    }
    .btn:hover{ background: var(--primary-600); }
    .btn:active{ transform: translateY(1px); }

    .btn.secondary{
      background:transparent;
      color: var(--primary);
      border: 1px solid var(--primary);
    }
    .btn.secondary:hover{
      background: rgba(41,158,230,.08);
    }

    .actions{
      display:flex;
      gap:12px;
      justify-content:flex-end;
      margin-top: 12px;
    }

    .alert{
      padding: 12px 16px;
      border-radius: 10px;
      font-weight:600;
      text-align:center;
      margin-bottom: 14px;
    }
    .alert-success{ background:#16a34a; color:#fff; }
    .alert-error{ background:#dc2626; color:#fff; }

    /* Ocultar lupa del sidebar en esta página si aplica */
    .sidebar .nav .search-item{ display:none; }
    .sidebar ul li:first-child{ display:none; } /* fallback */

    /* Mini separadores visuales */
    .divider{ height:1px; background: #e5e7eb; margin: 14px 0; }
    body.dark .divider{ background:#3b3f4a; }

    /* Chip de ejemplo dentro del form */
    .chip{
      display:inline-flex; align-items:center; gap:6px;
      font-size:12px; padding:6px 10px; border-radius:999px;
      background:#eef6ff; color:#074799; border:1px solid #cfe6ff;
    }
    body.dark .chip{ background:#19314a; color:#cfe6ff; border-color:#2a4a6b; }
  </style>
</head>
<body>
  <div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <main class="content with-fixed-sidebar">
      <h1 class="page-title">Ajustes de la cuenta</h1>

      <div class="settings-grid">

        <!-- Mensajes globales -->
        <?php if($success_msg): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
        <?php elseif($error_msg): ?>
          <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <form class="card" action="guardar_ajustes.php" method="POST" novalidate>
          <h3>Perfil</h3>
          <div class="row">
            <div>
              <label for="full_name">Nombre completo</label>
              <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
            </div>
            <div>
              <label for="username">Nombre de usuario</label>
              <input type="text" id="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled>
              <div class="help">Tu usuario no se puede modificar</div>
            </div>
          </div>

          <div class="divider"></div>

          <h3>Preferencias</h3>
          <div class="row">
            <div>
              <label for="currency_pref">Moneda preferida</label>
              <select name="currency_pref" id="currency_pref" required>
                <option value="S/."     <?= $current_currency == 'S/.'      ? 'selected' : '' ?>>S/. – Sol peruano (PEN)</option>
                <option value="$"       <?= $current_currency == '$'        ? 'selected' : '' ?>>$ – Dólar estadounidense (USD)</option>
                <option value="€"       <?= $current_currency == '€'        ? 'selected' : '' ?>>€ – Euro (EUR)</option>
                <option value="$ (MXN)" <?= $current_currency == '$ (MXN)'  ? 'selected' : '' ?>>$ – Peso mexicano (MXN)</option>
                <option value="$ (ARS)" <?= $current_currency == '$ (ARS)'  ? 'selected' : '' ?>>$ – Peso argentino (ARS)</option>
                <option value="$ (CLP)" <?= $current_currency == '$ (CLP)'  ? 'selected' : '' ?>>$ – Peso chileno (CLP)</option>
                <option value="COL$"    <?= $current_currency == 'COL$'     ? 'selected' : '' ?>>COL$ – Peso colombiano (COP)</option>
                <option value="VES"     <?= $current_currency == 'VES'      ? 'selected' : '' ?>>VES – Bolívar venezolano</option>
                <option value="$U"      <?= $current_currency == '$U'       ? 'selected' : '' ?>>$U – Peso uruguayo (UYU)</option>
                <option value="₲"       <?= $current_currency == '₲'        ? 'selected' : '' ?>>₲ – Guaraní paraguayo (PYG)</option>
                <option value="Bs"      <?= $current_currency == 'Bs'       ? 'selected' : '' ?>>Bs – Boliviano (BOB)</option>
                <option value="$ (EC)"  <?= $current_currency == '$ (EC)'   ? 'selected' : '' ?>>$ – Dólar ecuatoriano (EC)</option>
              </select>
              <div class="help">Se usa en tarjetas, tablas y reportes</div>
            </div>

            <div>
              <label for="time_format">Formato de hora</label>
              <select name="time_format" id="time_format" required>
                <option value="12h" <?= $current_fmt === '12h' ? 'selected' : '' ?>>12 horas (AM/PM)</option>
                <option value="24h" <?= $current_fmt === '24h' ? 'selected' : '' ?>>24 horas</option>
              </select>
              <div class="help">Tus horarios se mostrarán con este formato. (Por defecto 12h)</div>
            </div>
          </div>

          <div class="divider"></div>

          <h3>Zona horaria</h3>
          <div class="row">
            <div>
              <label for="country">País</label>
              <select id="country">
                <!-- Se llena por JS, dejando seleccionado el país que contenga la TZ actual -->
              </select>
              <div class="help">Solo países de Latinoamérica, USA y España</div>
            </div>
            <div>
              <label for="timezone">Zona horaria</label>
              <select name="timezone" id="timezone" required>
                <!-- Se llena por JS según país -->
              </select>
              <div class="help">Ejemplo de visualización: <span class="chip" id="tzPreview">—</span></div>
            </div>
          </div>

          <div class="actions">
            <button type="button" class="btn secondary" onclick="window.history.back()">Cancelar</button>
            <button type="submit" class="btn">Guardar cambios</button>
          </div>
        </form>

      </div>
    </main>
  </div>

  <script>
    // Dark mode persistente
    (function(){
      if(localStorage.getItem('darkMode')==='true'){
        document.body.classList.add('dark');
      }
    })();

    // ====== Timezones por país (LatAm + USA + España) ======
    // Lista curada de zonas comunes por país (PHP tz database)
    const COUNTRY_TZ = {
      "Argentina": ["America/Argentina/Buenos_Aires","America/Argentina/Cordoba","America/Argentina/Salta","America/Argentina/Mendoza","America/Argentina/Ushuaia"],
      "Bolivia": ["America/La_Paz"],
      "Brasil": ["America/Sao_Paulo","America/Manaus","America/Cuiaba","America/Fortaleza","America/Belem","America/Recife","America/Bahia","America/Porto_Velho","America/Boa_Vista"],
      "Chile": ["America/Santiago","America/Punta_Arenas","Pacific/Easter"],
      "Colombia": ["America/Bogota"],
      "Costa Rica": ["America/Costa_Rica"],
      "Cuba": ["America/Havana"],
      "República Dominicana": ["America/Santo_Domingo"],
      "Ecuador": ["America/Guayaquil","Pacific/Galapagos"],
      "El Salvador": ["America/El_Salvador"],
      "Guatemala": ["America/Guatemala"],
      "Honduras": ["America/Tegucigalpa"],
      "México": ["America/Mexico_City","America/Monterrey","America/Tijuana","America/Merida","America/Cancun","America/Mazatlan","America/Chihuahua","America/Hermosillo"],
      "Nicaragua": ["America/Managua"],
      "Panamá": ["America/Panama"],
      "Paraguay": ["America/Asuncion"],
      "Perú": ["America/Lima"],
      "Puerto Rico": ["America/Puerto_Rico"],
      "Uruguay": ["America/Montevideo"],
      "Venezuela": ["America/Caracas"],
      "Estados Unidos": ["America/New_York","America/Chicago","America/Denver","America/Los_Angeles","America/Phoenix","America/Anchorage","Pacific/Honolulu"],
      "España": ["Europe/Madrid","Atlantic/Canary"]
    };

    // Detección de país sugerido a partir de la TZ actual del usuario
    const currentTz   = <?= json_encode($current_tz) ?>;
    const tzToCountry = (() => {
      const map = {};
      for (const [country, zones] of Object.entries(COUNTRY_TZ)) {
        zones.forEach(z => map[z] = country);
      }
      return map;
    })();
    const currentCountry = tzToCountry[currentTz] || "Estados Unidos"; // fallback

    // Pintar <select id="country">
    const countrySel = document.getElementById('country');
    const tzSel = document.getElementById('timezone');
    const tzPreview = document.getElementById('tzPreview');

    function fillCountries(){
      const countries = Object.keys(COUNTRY_TZ);
      countries.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c; opt.textContent = c;
        if (c === currentCountry) opt.selected = true;
        countrySel.appendChild(opt);
      });
    }

    function prettyTzName(tz){
      // Convierte "America/Mexico_City" a "America — Mexico City"
      const [area, cityRaw] = tz.split('/');
      const city = (cityRaw || '').replaceAll('_',' ');
      return area + (city ? ' — ' + city : '');
    }

    function fillTimezones(country){
      tzSel.innerHTML = '';
      const zones = COUNTRY_TZ[country] || [];
      zones.forEach(z => {
        const opt = document.createElement('option');
        opt.value = z; opt.textContent = prettyTzName(z);
        if (z === currentTz) opt.selected = true;
        tzSel.appendChild(opt);
      });
      updatePreview();
    }

    function updatePreview(){
      const tz = tzSel.value;
      const fmt = document.getElementById('time_format').value;
      // Mostramos un ejemplo local del formato: 02:45 PM / 14:45
      try{
        const now = new Date();
        // Para vista previa, mostramos HH:mm según selección (no convertimos al TZ aquí por simplicidad del lado cliente).
        const options = { hour: 'numeric', minute: '2-digit', hour12: (fmt === '12h') };
        const ex = new Intl.DateTimeFormat(undefined, options).format(now);
        tzPreview.textContent = `${prettyTzName(tz)} · ${ex}`;
      }catch(e){
        tzPreview.textContent = prettyTzName(tz);
      }
    }

    countrySel.addEventListener('change', e => fillTimezones(e.target.value));
    tzSel.addEventListener('change', updatePreview);
    document.getElementById('time_format').addEventListener('change', updatePreview);

    // Init
    fillCountries();
    fillTimezones(currentCountry);
  </script>
</body>
</html>
