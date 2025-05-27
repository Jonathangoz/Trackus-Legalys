<?php
  session_start();

  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      header("Location: logging");
      exit;
  }

  if($_SESSION['tipo_rol'] !== 'ADMIN'){
    header("Location: logging");
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
    <title>Deudores de Bancos de Cobro Coactivo SENA Regional Santander</title>
    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="CSS/Deudores.css">
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
            <h1>Deudores - Cobro Coactivo</h1>
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
                    <li><a href="module_login/logout.php" class="sesion"><i class="fa-solid fa-right-from-bracket me-2" style="margin-right: 15px;"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>
    
    <main class="container">
        <h1 class="main-title">Sistema de Deudores de Bancos de Cobro Coactivo</h1>
        
        <section class="search-box">
            <form class="search-form">
                <div class="form-group">
                    <label for="documento">Número de Documento</label>
                    <input type="text" id="documento" placeholder="Ingrese número de documento">
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" placeholder="Ingrese nombre">
                </div>
                <div class="form-group">
                    <label for="tipo-proceso">Tipo de Proceso</label>
                    <select id="tipo-proceso">
                        <option value="">Todos</option>
                        <option value="multa">Multa</option>
                        <option value="contrato">Incumplimiento de Contrato</option>
                        <option value="deuda">Deuda Monetaria</option>
                        <option value="convenio">Convenio</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="resuelto">Resuelto</option>
                    </select>
                </div>
                <div class="search-buttons">
                    <button type="button" class="btn" id="resetBtn">Limpiar</button>
                    <button type="button" class="btn" id="searchBtn">Buscar</button>
                </div>
            </form>
        </section>
        
        <section class="results-box">
            <div class="results-header">
                <h2 class="results-title">Resultados</h2>
                <span class="results-count">Total: <span id="resultCount">15</span></span>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Tipo de Proceso</th>
                            <th>Monto</th>
                            <th>Fecha Inicio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="resultsTable">
                        <!-- Data will be populated dynamically -->
                    </tbody>
                </table>
            </div>
            
            <div class="pagination" id="pagination">
                <!-- Pagination will be populated dynamically -->
            </div>
        </section>
    </main>
    
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="footer-title">SENA Regional Santander</h3>
                <p>Sistema de Seguimiento de Deudores de Bancos de Cobro Coactivo</p>
                <p>Dirección: Calle 16 # 27-37, Bucaramanga, Santander</p>
                <p>Teléfono: (607) 652-5252</p>
            </div>
            
            <div class="footer-section">
                <h3 class="footer-title">Enlaces Rápidos</h3>
                <ul class="footer-links">
                    <li><a href="#">Página Principal</a></li>
                    <li><a href="#">Consulta de Procesos</a></li>
                    <li><a href="#">Preguntas Frecuentes</a></li>
                    <li><a href="#">Transparencia</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3 class="footer-title">Servicios</h3>
                <ul class="footer-links">
                    <li><a href="#">Acuerdos de Pago</a></li>
                    <li><a href="#">Consulta de Deudas</a></li>
                    <li><a href="#">Estado de Procesos</a></li>
                    <li><a href="#">Certificados</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 SENA Regional Santander - Todos los derechos reservados</p>
        </div>

    <script src="js/Deudores.js"></script>
    <!-- Scripts del Boton -->
    <script src="js/back-to-top.js"></script>
</body>
</html>