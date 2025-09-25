// js/script.js
(function () {
  // Corre cuando el DOM esté listo (por si el script se incluye en <head>)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    const sidebar   = document.querySelector('.sidebar');
    const closeBtn  = document.querySelector('.sidebar .logo-details #btn'); // específico
    const searchBtn = document.querySelector('.sidebar .bx-search');
    const searchInp = document.querySelector('.sidebar input[type="text"]');

    if (!sidebar || !closeBtn) return; // nada que hacer

    function updateMenuIcon() {
      const isOpen = sidebar.classList.contains('open');
      if (isOpen) {
        if (closeBtn.classList.contains('bx-menu')) {
          closeBtn.classList.replace('bx-menu', 'bx-menu-alt-right');
        }
      } else {
        if (closeBtn.classList.contains('bx-menu-alt-right')) {
          closeBtn.classList.replace('bx-menu-alt-right', 'bx-menu');
        }
      }
    }

    function toggleSidebar() {
      sidebar.classList.toggle('open');
      updateMenuIcon();
    }

    function openSidebar() {
      if (!sidebar.classList.contains('open')) {
        sidebar.classList.add('open');
        updateMenuIcon();
      }
    }

    // Click en el botón menú
    closeBtn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      toggleSidebar();
    });

    // Accesibilidad por teclado
    closeBtn.setAttribute('tabindex', '0');
    closeBtn.setAttribute('role', 'button');
    closeBtn.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        toggleSidebar();
      }
    });

    // Lupa: siempre abre y enfoca (no togglea)
    if (searchBtn) {
      searchBtn.addEventListener('click', function () {
        openSidebar();
        if (searchInp) setTimeout(() => searchInp.focus(), 60);
      });
    }

    // Sincroniza icono con estado inicial
    updateMenuIcon();
  }
})();
