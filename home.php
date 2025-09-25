<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>ShockFy - Control de Ventas e Inventario</title>
  <link rel="icon" href="assets/img/favicon.png" type="image/png">
  <link rel="shortcut icon" href="assets/img/favicon.png" type="image/png">
  <!-- Fuente opcional -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <!-- Si tienes un CSS propio puedes mantenerlo, pero este archivo ya es autosuficiente -->
  <!-- <link href="css/home.css" rel="stylesheet"> -->

  <style>
    :root{
      /* Paleta con más contraste y menos “blanco plano” */
      --bg: #eef2ff;      /* azul muy claro */
      --panel:#ffffff;
      --text:#0b1220;     /* más oscuro */
      --muted:#475569;    /* secundarios con mejor contraste */
      --primary:#2344ec;  /* azul vibrante principal */
      --primary-2:#5ea4ff;
      --success:#16a34a;
      --warning:#f59e0b;
      --border:#e5e7eb;
      --shadow:0 18px 40px rgba(2,6,23,.08);
      --radius:18px;
      --max:1200px;
    }
    *{box-sizing:border-box}
    html,body{
      margin:0;
      font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      color:var(--text);
      background:
        radial-gradient(900px 700px at -10% -20%, #e3e9ff 0%, transparent 60%),
        radial-gradient(900px 700px at 110% -20%, #edf3ff 0%, transparent 60%),
        #f6f8ff;
    }
    a{color:inherit;text-decoration:none}

    /* ====== NAV ====== */
    nav{position:sticky;top:0;z-index:1000;background:rgba(255,255,255,.78);backdrop-filter:blur(8px);border-bottom:1px solid var(--border)}
    .nav-container{max-width:var(--max);margin:0 auto;display:flex;align-items:center;justify-content:space-between;padding:14px 18px}
    .logo{font-weight:800;letter-spacing:.2px;display:flex;align-items:center;gap:10px}
    .nav-links{display:flex;gap:16px;align-items:center}
    .nav-links a{padding:10px 12px;border-radius:10px;font-weight:600;color:#0f172a}
    .nav-links a:hover{background:#eef2ff}
    .cta-button{background:linear-gradient(135deg,var(--primary),var(--primary-2));color:#fff !important;padding:10px 16px;border-radius:12px;box-shadow:var(--shadow);border:1px solid #cfe0ff}
    .login-button{background:#fff !important;color:var(--text) !important;border:1px solid var(--border)}
    .login-button:hover{background:#f8fafc}

    .mobile-menu-toggle{display:none;background:transparent;border:0;cursor:pointer}
    .mobile-menu-toggle span{display:block;width:24px;height:2px;background:#0f172a;margin:5px 0}
    .mobile-nav{display:none;border-top:1px solid var(--border);background:#fff}
    .mobile-nav a{display:block;padding:14px 18px;border-bottom:1px solid var(--border);font-weight:600}

    @media (max-width:880px){
      .nav-links{display:none}
      .mobile-menu-toggle{display:block}
    }

    /* ====== HERO ====== */
    .hero{position:relative;overflow:hidden}
    .hero-inner{max-width:var(--max);margin:0 auto;display:grid;grid-template-columns:1.2fr .8fr;gap:24px;align-items:center;padding:64px 18px 42px}
    .hero h1{
      font-size:40px;line-height:1.05;margin:0 0 12px;font-weight:800;
      color:#0b2cff;              /* Más visible */
      text-shadow:0 1px 0 rgba(255,255,255,.6);
    }
    /* Alternativa en gradiente (opcional):
    .hero h1{
      background: linear-gradient(90deg, #0b2cff, #5ea4ff);
      -webkit-background-clip:text;background-clip:text;color:transparent;
    } */
    .hero-subtitle{color:#374151;font-size:16px;max-width:620px}
    .hero-ctas{display:flex;gap:12px;flex-wrap:wrap;margin-top:18px}
    .btn{padding:12px 16px;border-radius:12px;font-weight:800;border:1px solid var(--border)}
    .btn-primary{background:linear-gradient(135deg,var(--primary),var(--primary-2));color:#fff;border-color:#cfe0ff}
    .btn-ghost{background:#fff;color:#0f172a}
    .btn-ghost:hover{background:#eef2ff}
    .trust-row{display:flex;align-items:center;gap:14px;margin-top:18px;color:var(--muted);font-size:13px}
    .trust-row .ic{width:18px;height:18px;color:#16a34a}

    .hero-art{
      background:linear-gradient(180deg,#ffffff,#f0f4ff);
      border:1px solid #d6e1ff;border-radius:16px;padding:18px;box-shadow:var(--shadow);
    }
    .hero-screenshot{
      width:100%;border-radius:12px;border:1px solid #e9eefb;box-shadow:0 10px 30px rgba(37,99,235,.12);
      object-fit:cover;background:#f6f8ff;aspect-ratio:16/10
    }

    /* ====== SECCIONES ====== */
    .section{padding:56px 18px;background:linear-gradient(180deg,#fbfdff,#eef2ff)}
    .wrap{max-width:var(--max);margin:0 auto}
    .section h2{font-size:28px;margin:0 0 8px;font-weight:800}
    .section .lead{color:var(--muted);max-width:760px}

    /* Imagen grande (toma en cuenta tu nota original de margen) */
    .feature-image-container{margin:26px auto 35px;max-width:var(--max)} /* aquí el margen inferior */
    .feature-image{width:100%;border-radius:16px;border:1px solid #e9eefb;box-shadow:var(--shadow);object-fit:cover}

    /* ====== FEATURES GRID ====== */
    .features-four-col{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-top:16px}
    .feature-card-small{background:#fff;border:1px solid #dbe4ff;border-radius:14px;padding:16px;box-shadow:0 10px 28px rgba(35,68,236,.07)}
    .feature-card-small h4{margin:10px 0 6px}
    .feature-card-small p{color:var(--muted);font-size:14px;margin:0}
    .feature-icon-small{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#e8efff,#f3f7ff);display:grid;place-items:center;border:1px solid #d6e1ff}

    @media (max-width:980px){.hero-inner{grid-template-columns:1fr}.features-four-col{grid-template-columns:1fr 1fr}}
    @media (max-width:640px){.features-four-col{grid-template-columns:1fr}}

    /* ====== HOW IT WORKS ====== */
    .steps{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:20px}
    .step{background:#fff;border:1px solid #dbe4ff;border-radius:14px;padding:16px;box-shadow:0 10px 28px rgba(35,68,236,.07)}
    .step .badge{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;border:1px solid var(--border);font-weight:700;font-size:12px;background:#f8fafc}
    .step p{color:var(--muted);font-size:14px}
    @media (max-width:880px){.steps{grid-template-columns:1fr}}

    /* ====== TRUST ====== */
    .trust{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:20px}
    .trust .card{background:#fff;border:1px solid #dbe4ff;border-radius:14px;padding:16px;box-shadow:0 10px 28px rgba(35,68,236,.07)}
    .trust .card p{color:var(--muted);font-size:14px;margin:6px 0 0}
    @media (max-width:880px){.trust{grid-template-columns:1fr}}

    /* ====== PRICING (Plan único) ====== */
    .pricing{display:grid;grid-template-columns:1fr;gap:16px;margin-top:22px}
    .plan{background:#fff;border:1px solid #dbe4ff;border-radius:16px;padding:22px;box-shadow:0 10px 28px rgba(35,68,236,.07)}
    .price{font-size:40px;font-weight:800;letter-spacing:-.5px}
    .per{color:var(--muted);font-size:14px}
    .ul{margin:12px 0 0;padding:0;list-style:none}
    .ul li{display:flex;gap:10px;align-items:flex-start;margin:8px 0;color:#0f172a}
    .ul .ok{color:#16a34a}
    .plan-cta{margin-top:16px;display:flex;gap:10px;flex-wrap:wrap}
    .guarantee{display:flex;align-items:center;gap:8px;color:var(--muted);font-size:13px;margin-top:10px}

    /* ====== TESTIMONIOS ====== */
    .testimonials{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:20px}
    .t{background:#fff;border:1px solid #dbe4ff;border-radius:14px;padding:16px;box-shadow:0 10px 28px rgba(35,68,236,.07)}
    .t .who{font-weight:700}
    .t p{color:var(--muted);margin:8px 0 0}
    @media (max-width:980px){.testimonials{grid-template-columns:1fr}}

    /* ====== FAQ ====== */
    .faq{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:20px}
    .qa{background:#fff;border:1px solid #dbe4ff;border-radius:14px;padding:16px;box-shadow:0 10px 28px rgba(35,68,236,.07)}
    .qa h4{margin:0 0 6px}
    .qa p{color:var(--muted);margin:0}
    @media (max-width:880px){.faq{grid-template-columns:1fr}}

    /* ====== FOOTER ====== */
    .footer{border-top:1px solid var(--border);background:#fff}
    .footer-inner{max-width:var(--max);margin:0 auto;padding:20px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
    .footer .brand{font-weight:800}
    .social a{color:var(--muted);padding:8px 10px;border-radius:8px}
    .social a:hover{background:#eef2ff}


    /* ==== FIX hover CTA "Pruébalo gratis" ==== */
.cta-button{
  background: linear-gradient(135deg, var(--primary), var(--primary-2)) !important;
  color:#fff !important;
  border: 1px solid #cfe0ff !important;
}
.cta-button:hover,
.cta-button:focus{
  /* Mantén contraste: no cambiar a blanco */
  color:#fff !important;
  filter: brightness(0.92);
  box-shadow: 0 8px 24px rgba(35,68,236,.25);
}
.cta-button:focus-visible{
  outline: 3px solid rgba(35,68,236,.35);
  outline-offset: 2px;
  border-radius: 12px;
}

/* (Opcional) coherencia con botones primarios del resto de la página */
.btn-primary:hover,
.btn-primary:focus{
  color:#fff !important;
  filter: brightness(0.92);
  box-shadow: 0 8px 24px rgba(35,68,236,.25);
}
.btn-primary:focus-visible{
  outline: 3px solid rgba(35,68,236,.35);
  outline-offset: 2px;
}


    
  </style>
</head>
<body>

  <!-- NAV -->
  <nav>
    <div class="nav-container">
      <div class="logo">
        <img src="assets/img/icono_menu.png" alt="ShockFy" style="height:34px">
        ShockFy
      </div>
      <div class="nav-links">
        <a href="#features">Características</a>
        <a href="#how">Cómo funciona</a>
        <a href="#pricing">Precio</a>
        <a href="#faq">FAQ</a>
        <a href="signup.php" class="cta-button">Pruébalo gratis</a>
        <a href="login.php" class="cta-button login-button">Iniciar sesión</a>
      </div>
      <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Abrir menú">
        <span></span><span></span><span></span>
      </button>
    </div>
    <div class="mobile-nav" id="mobileNav">
      <a href="#features">Características</a>
      <a href="#how">Cómo funciona</a>
      <a href="#pricing">Precio</a>
      <a href="#faq">FAQ</a>
      <a href="signup.php" class="cta-button">Pruébalo gratis</a>
      <a href="login.php" class="cta-button login-button">Iniciar sesión</a>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <div>
        <h1>Controla tus ventas e inventario con confianza.</h1>
        <p class="hero-subtitle">
          Crea tu catálogo, registra ventas en segundos y obtén métricas claras (ingresos, costos y ganancias). Diseñado para emprendedores que quieren simplicidad y velocidad.
        </p>
        <div class="hero-ctas">
          <a href="signup.php" class="btn btn-primary">Iniciar prueba gratis 15 días</a>
          <a href="login.php" class="btn btn-ghost">Ya tengo cuenta</a>
        </div>
        <div class="trust-row">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 6 9 17l-5-5"/></svg>
          Sin tarjeta • Cancela cuando quieras • Datos seguros (HTTPS/SSL)
        </div>
      </div>
      <div class="hero-art">
        <img class="hero-screenshot" src="assets/img/imagen2.png" alt="Vista del dashboard de ShockFy">
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section class="section" id="features">
    <div class="wrap">
      <h2>Todo tu inventario bajo control</h2>
      <p class="lead">Herramientas pensadas para simplificar tu operación y ayudarte a vender más.</p>

      <div class="feature-image-container">
        <img src="assets/img/imagen2.png" class="feature-image" alt="ShockFy interfaz" />
      </div>

      <div class="features-four-col">
        <div class="feature-card-small">
          <div class="feature-icon-small">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          </div>
          <h4>Simpleza</h4>
          <p>Flujo de venta sin fricción, edición en línea y validaciones de stock.</p>
        </div>
        <div class="feature-card-small">
          <div class="feature-icon-small">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M19 13a4 4 0 0 1-4 4H7"/></svg>
          </div>
          <h4>Análisis</h4>
          <p>Ventas por día, ganancia neta y valor de inventario siempre visibles.</p>
        </div>
        <div class="feature-card-small">
          <div class="feature-icon-small">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22"/><path d="M17 5H9a4 4 0 0 0 0 8h8a4 4 0 0 1 0 8H7"/></svg>
          </div>
          <h4>Precio justo</h4>
          <p>Un solo plan, todo incluido. Sin sorpresas ni cargos ocultos.</p>
        </div>
        <div class="feature-card-small">
          <div class="feature-icon-small">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg>
          </div>
          <h4>Monedas</h4>
          <p>Soporte para USD, EUR y monedas de LATAM. Adáptalo a tu negocio.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section class="section" id="how">
    <div class="wrap">
      <h2>Cómo funciona</h2>
      <p class="lead">En 3 pasos estás vendiendo y midiendo resultados.</p>
      <div class="steps">
        <div class="step">
          <span class="badge">Paso 1</span>
          <h4>Regístrate y configura</h4>
          <p>Elige tu moneda y crea tus primeras categorías y productos.</p>
        </div>
        <div class="step">
          <span class="badge">Paso 2</span>
          <h4>Vende en segundos</h4>
          <p>Carrito multiproducto con validación de stock y precios editables.</p>
        </div>
        <div class="step">
          <span class="badge">Paso 3</span>
          <h4>Mide y mejora</h4>
          <p>Dashboard con ventas del mes, ganancia y ranking de productos.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- TRUST / RAZONES -->
  <section class="section">
    <div class="wrap">
      <h2>Diseñado para inspirar confianza</h2>
      <p class="lead">Tu información es tuya. Nos enfocamos en seguridad y soporte.</p>
      <div class="trust">
        <div class="card">
          <strong>Seguridad</strong>
          <p>Conexión cifrada (HTTPS/SSL). Buenas prácticas de almacenamiento.</p>
        </div>
        <div class="card">
          <strong>Soporte humano</strong>
          <p>Atención rápida por chat y correo. Te ayudamos a empezar.</p>
        </div>
        <div class="card">
          <strong>Actualizaciones</strong>
          <p>Mejoras continuas sin afectar tu operación diaria.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- PRICING -->
  <section class="section" id="pricing">
    <div class="wrap">
      <h2>Un solo plan. Todo incluido.</h2>
      <p class="lead">Empieza hoy con una prueba gratuita de 15 días. Cancela cuando quieras.</p>
      <div class="pricing">
        <div class="plan">
          <div class="price">US$ 9.99 <span class="per">/ mes</span></div>
          <ul class="ul">
            <li><span class="ok">✔</span> Inventario y ventas ilimitadas</li>
            <li><span class="ok">✔</span> Carrito multiproducto</li>
            <li><span class="ok">✔</span> Reportes y métricas del mes</li>
            <li><span class="ok">✔</span> Soporte prioritario</li>
          </ul>
          <div class="plan-cta">
            <a href="signup.php" class="btn btn-primary">Comenzar prueba gratis</a>
            <a href="login.php" class="btn btn-ghost">Iniciar sesión</a>
          </div>
          <div class="guarantee">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            Sin tarjeta • Cancela cuando quieras
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- TESTIMONIOS -->
  <section class="section">
    <div class="wrap">
      <h2>Lo que dicen nuestros usuarios</h2>
      <div class="testimonials">
        <div class="t">
          <div class="who">María · Tienda de ropa</div>
          <p>“En 1 día tenía todo cargado y vendiendo. El reporte del mes me ahorra horas.”</p>
        </div>
        <div class="t">
          <div class="who">Carlos · Calzado</div>
          <p>“El carrito multiproducto y el stock me evitaron errores. Súper simple.”</p>
        </div>
        <div class="t">
          <div class="who">Ana · Accesorios</div>
          <p>“Precio justo y soporte rápido. Exactamente lo que necesitaba.”</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section" id="faq">
    <div class="wrap">
      <h2>Preguntas frecuentes</h2>
      <div class="faq">
        <div class="qa">
          <h4>¿La prueba gratis requiere tarjeta?</h4>
          <p>No. Crea tu cuenta, prueba 15 días y decide después.</p>
        </div>
        <div class="qa">
          <h4>¿Puedo cancelar cuando quiera?</h4>
          <p>Sí. Sin permanencias ni cargos ocultos.</p>
        </div>
        <div class="qa">
          <h4>¿Puedo cambiar la moneda?</h4>
          <p>Sí, puedes elegir tu moneda preferida (USD, EUR, LATAM).</p>
        </div>
        <div class="qa">
          <h4>¿Cómo contacto soporte?</h4>
          <p>Desde la app o por correo. Respondemos muy rápido.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="footer-inner">
      <div class="brand">ShockFy</div>
      <div class="copy">© <?= date('Y') ?> ShockFy. Todos los derechos reservados.</div>
      <div class="social">
        <a href="#">Twitter</a>
        <a href="#">LinkedIn</a>
        <a href="#">GitHub</a>
      </div>
    </div>
  </footer>

  <script>
    // Menú móvil
    const toggler = document.getElementById('mobileMenuToggle');
    const mobileNav = document.getElementById('mobileNav');
    toggler?.addEventListener('click', ()=> {
      mobileNav.style.display = mobileNav.style.display === 'block' ? 'none' : 'block';
    });

    // Scroll suave en anclas
    document.querySelectorAll('a[href^="#"]').forEach(a=>{
      a.addEventListener('click', e=>{
        const id = a.getAttribute('href');
        if(id.length>1){
          e.preventDefault();
          document.querySelector(id)?.scrollIntoView({behavior:'smooth',block:'start'});
          mobileNav.style.display='none';
        }
      });
    });
  </script>
</body>
</html>
