<?php
// sidebar.php
?>
<!-- Favicon -->
<link rel="icon" type="image/png" href="assets/img/favicon.png">

<!-- Sidebar -->
<style>
/* Google Font Link */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
*{
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins" , sans-serif;
}
.sidebar{
  position: fixed;
  left: 0;
  top: 0;
  height: 100%;
  width: 78px;
  background: #11101D;
  padding: 6px 14px;
  z-index: 99;
  transition: all 0.5s ease;
}
.sidebar.open{ width: 250px; }

.sidebar .logo-details{
  height: 60px;
  display: flex;
  align-items: center;
  position: relative;
}

/* Logo oculto por defecto */
.sidebar .logo-details img.logo-icon{
  height: 40px;
  width: 40px;
  margin-right: 8px;
  object-fit: contain;
  opacity: 0;
  visibility: hidden;
  transition: all 0.4s ease;
}

/* Mostrar logo solo cuando el sidebar esté abierto */
.sidebar.open .logo-details img.logo-icon{
  opacity: 1;
  visibility: visible;
}

.sidebar .logo-details .icon{
  opacity: 0;
  transition: all 0.5s ease;
}
.sidebar .logo-details .logo_name{
  color: #fff;
  font-size: 20px;
  font-weight: 600;
  opacity: 0;
  transition: all 0.5s ease;
}
.sidebar.open .logo-details .icon,
.sidebar.open .logo-details .logo_name{
  opacity: 1;
}
.sidebar .logo-details #btn{
  position: absolute;
  top: 50%;
  right: 0;
  transform: translateY(-50%);
  font-size: 23px;
  cursor: pointer;
  transition: all 0.5s ease;
}
.sidebar i{
  color: #fff;
  height: 60px;
  min-width: 50px;
  font-size: 28px;
  text-align: center;
  line-height: 60px;
}
.sidebar .nav-list{
  margin-top: 20px;
  height: 100%;
}
.sidebar li{
  position: relative;
  margin: 8px 0;
  list-style: none;
}
.sidebar li .tooltip{
  position: absolute;
  top: -20px;
  left: calc(100% + 15px);
  z-index: 3;
  background: #0c95b8ff;
  box-shadow: 0 5px 10px rgba(0,0,0,0.3);
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 15px;
  opacity: 0;
  white-space: nowrap;
  pointer-events: none;
  transition: 0s;
}
.sidebar li:hover .tooltip{
  opacity: 1;
  pointer-events: auto;
  transition: all 0.4s ease;
  top: 50%;
  transform: translateY(-50%);
}
.sidebar.open li .tooltip{ display: none; }
.sidebar input{
  font-size: 15px;
  color: #FFF;
  font-weight: 400;
  outline: none;
  height: 50px;
  width: 50px;
  border: none;
  border-radius: 12px;
  transition: all 0.5s ease;
  background: #1d1b31;
}
.sidebar.open input{
  padding: 0 20px 0 50px;
  width: 100%;
}
.sidebar .bx-search{
  position: absolute;
  top: 50%;
  left: 0;
  transform: translateY(-50%);
  font-size: 22px;
  background: #1d1b31;
  color: #FFF;
}
.sidebar .bx-search:hover{
  background: #FFF;
  color: #11101d;
}
.sidebar li a{
  display: flex;
  height: 100%;
  width: 100%;
  border-radius: 12px;
  align-items: center;
  text-decoration: none;
  transition: all 0.4s ease;
  background: #11101D;
}
.sidebar li a:hover{ background: #FFF; }
.sidebar li a .links_name{
  color: #fff;
  font-size: 15px;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: 0.4s;
}
.sidebar.open li a .links_name{
  opacity: 1;
  pointer-events: auto;
}
.sidebar li a:hover .links_name,
.sidebar li a:hover i{
  transition: all 0.5s ease;
  color: #11101D;
}
.sidebar li i{
  height: 50px;
  line-height: 50px;
  font-size: 18px;
  border-radius: 12px;
}
.sidebar li.profile{
  position: fixed;
  height: 60px;
  width: 78px;
  left: 0;
  bottom: -8px;
  padding: 10px 14px;
  background: #1d1b31;
  transition: all 0.5s ease;
  overflow: hidden;
}
.sidebar.open li.profile{ width: 250px; }
.sidebar li .profile-details{
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
}
.sidebar li img{
  height: 45px;
  width: 45px;
  object-fit: cover;
  border-radius: 6px;
  margin-right: 10px;
}
.sidebar li.profile .name,
.sidebar li.profile .job{
  font-size: 15px;
  font-weight: 400;
  color: #fff;
  white-space: nowrap;
}
.sidebar li.profile .job{ font-size: 12px; }
.sidebar .profile #log_out{
  position: absolute;
  top: 50%;
  right: 0;
  transform: translateY(-50%);
  background: #1d1b31;
  width: 100%;
  height: 60px;
  line-height: 60px;
  border-radius: 0;
  transition: all 0.5s ease;
}
.sidebar.open .profile #log_out{
  width: 50px;
  background: none;
}

/* Botón modo oscuro */
.dark-toggle-btn {
  position: fixed;
  top: 15px;
  right: 20px;
  z-index: 1000;
  background: #3498db;
  color: #fff;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}
</style>

<link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">

<div class="sidebar">
  <div class="logo-details">
    <img src="assets/img/icono_menu.png" alt="Logo" class="logo-icon">
    <div class="logo_name">ShockFy</div>
    <i class="bx bx-menu" id="btn"></i>
  </div>
  <ul class="nav-list">
    <li>
      <i class="bx bx-search"></i>
      <input type="text" placeholder="Buscar...">
      <span class="tooltip">Buscar</span>
    </li>
    <li>
      <a href="index.php">
        <i class="bx bx-grid-alt"></i>
        <span class="links_name">Principal</span>
      </a>
      <span class="tooltip">Principal</span>
    </li>
    <li>
      <a href="add_product.php">
        <i class="bx bx-plus-circle"></i>
        <span class="links_name">Agregar producto</span>
      </a>
      <span class="tooltip">Agregar producto</span>
    </li>
    <li>
      <a href="products.php">
        <i class="bx bx-package"></i>
        <span class="links_name">Productos</span>
      </a>
      <span class="tooltip">Productos</span>
    </li>
    <li>
      <a href="categories.php">
        <i class="bx bx-category"></i>
        <span class="links_name">Categorías</span>
      </a>
      <span class="tooltip">Categorías</span>
    </li>
    <li>
      <a href="sell.php">
        <i class="bx bx-cart-alt"></i>
        <span class="links_name">Registrar venta</span>
      </a>
      <span class="tooltip">Registrar venta</span>
    </li>
    <li>
      <a href="sales_report.php">
        <i class="bx bx-bar-chart"></i>
        <span class="links_name">Estadísticas</span>
      </a>
      <span class="tooltip">Estadísticas</span>
    </li>

    <!-- ✅ Botón Ajustes -->
    <li>
      <a href="ajustes.php">
        <i class="bx bx-cog"></i>
        <span class="links_name">Ajustes</span>
      </a>
      <span class="tooltip">Ajustes</span>
    </li>

    <li class="profile">
      <div class="profile-details">
        <img src="iconos/user.png" alt="Imagen de perfil">
        <div class="name_job">
          <div class="name">
            <?php 
            echo isset($_SESSION['full_name']) && $_SESSION['full_name'] != '' 
                 ? htmlspecialchars($_SESSION['full_name']) 
                 : (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Usuario'); 
            ?>
          </div>
          <div class="job">
            <?php 
            echo isset($_SESSION['role']) && $_SESSION['role'] != '' 
                 ? htmlspecialchars($_SESSION['role']) 
                 : 'Invitado'; 
            ?>
          </div>
        </div>
      </div>
      <a href="logout.php" id="log_out"><i class="bx bx-log-out"></i></a>
    </li>
  </ul>
</div>

<script src="js/script.js"></script>
