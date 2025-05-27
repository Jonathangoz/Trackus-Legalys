<?php
  session_start();

  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      header("Location: logging.php");
      exit;
  }

  if($_SESSION['tipo_rol'] !== 'ADMIN'){
    header("Location: logging.php");
    session_destroy();
    session_unset();
    exit;
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesos de Cobro Coactivo - SENA Regional Santander</title>
    <link rel="stylesheet" href="../CSS/procesos.css">
     <!-- Favicon -->
     <link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
     <!-- Fonts -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Botón de volver arriba -->
    <button type="button" id="backToTopBtn" title="Volver arriba">
        <i class="fa-solid fa-arrow-up fa-lg" aria-hidden="true"></i>
    </button>
    <!-- Encabezado del gobierno -->
    <div class="top">
        <a href="https://www.gov.co" target="_blank" alt="Gov.co" rel="noopener noreferrer">
            <img class="gov" src="https://css.mintic.gov.co/mt/mintic/img/header_govco.png" alt="Gov Co">
        </a>
    </div>
    <header>
        <div class="logo-container">
            <img src="img/sena.blanco.png" class="img-sena" alt="Logo SENA">            
            <p>SENA Regional Santander</p>
        </div>
        <div class="title-container">
            <h1>Procesos de Cobro Coactivo</h1>
        </div>       
        <div class="user-home">
            <div class="home">
                <a href="dashboard.php" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Dashboard</span>
                    </div>
                </a>
            </div>
            <div class="dropdown" id="dropdownContainer">
                <input type="checkbox" id="userToggle" hidden>
                <label for="userToggle" class="dropdown-toggle">
                    <span class="user-name">Bienvenido, Admin</span>
                    <div class="avatar">
                        <i class="fa-solid fa-user fa-lg" style="margin-right: 6px;"></i>
                        <i class="fa-solid fa-caret-down" style="margin-right: 6px;"></i>
                    </div>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="#" class="drop"><i class="fa-solid fa-user me-2" style="margin-right: 15px;"></i>Perfil</a></li>
                    <li><a href="#" class="drop"><i class="fa-solid fa-gear me-2" style="margin-right: 15px;"></i>Configuración</a></li>
                    <li><hr></li>
                    <li><a href="../module_login/logout.php" class="sesion"><i class="fa-solid fa-right-from-bracket me-2" style="margin-right: 15px;"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>
    
    <div class="container">
        <section class="search-section">
            <h2>Consulta de Procesos</h2>
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Número de documento o radicado">
                <select id="search-type">
                    <option value="documento">Documento de identidad</option>
                    <option value="radicado">Número de radicado</option>
                    <option value="nombre">Nombre del deudor</option>
                </select>
                <i class="fa-solid fa-chevron-down"></i>
                <button id="search-btn">Buscar</button>
            </div>
        </section>
        <section class="stats-section">
            <div class="stat-card">
                <div class="stat-title">Total de Procesos</div>
                <div class="stat-number">238</div>
                <div>Procesos activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Recaudo Total</div>
                <div class="stat-number">$1.2M</div>
                <div>Pesos colombianos</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Resueltos este mes</div>
                <div class="stat-number">17</div>
                <div>Procesos</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">En ejecución</div>
                <div class="stat-number">124</div>
                <div>Procesos</div>
            </div>
        </section>    
        <section class="process-section">
            <div class="process-header">
                <h2>Listado de Procesos</h2>
                <select id="filter-status">
                    <option value="todos">Todos los estados</option>
                    <option value="en-proceso">En proceso</option>
                    <option value="finalizado">Finalizado</option>
                    <option value="suspendido">Suspendido</option>
                </select>
            </div>
            <div class="process-list">
                <div class="process-list-header">
                    <div>No. Radicado</div>
                    <div>Deudor</div>
                    <div>Valor</div>
                    <div>Fecha Inicio</div>
                    <div>Estado</div>
                </div>
                <!-- Los items serán cargados dinamicamente con JavaScript -->
            </div>            
            <div id="process-details" class="process-detail">
            <!-- Detalles del proceso seleccionado serán cargados aquí -->
            </div>            
            <div class="pagination">
                <button id="prev-page">←</button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <button id="next-page">→</button>
            </div>
        </section>
    </div>    
    <footer>
        <p>Regional Santander - Área de Cobro Coactivo</p>
        <p>Dirección: Calle 16 No. 27-37 Bucaramanga</p>
        <p>Teléfono: (607) 6800600</p>
        <p>© 2025 SENA - Servicio Nacional de Aprendizaje</p>
    </footer>

    <script src="js/back-to-top.js"></script>
    <script src="js/Procesos.js"></script>
</body>
</html>   